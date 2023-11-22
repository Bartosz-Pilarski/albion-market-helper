<?php
    require "env.php";

    //15 minutes in Unix time
    $timeUntilOutdated = 900;

    $mysqli = new mysqli($host, $username, $password, $dbname);

    if($mysqli->connect_errno) {
        die("Connection error: ".$mysqli->connect_error);
    }

    /**
     * Fetches an item's timestamp from the database.
     * @param mysqli $db Database connection
     * @param string $name Item name to look up, structured as TX_$type, where X is a number between 2-8
     * 
     * @return int Unix timestamp of the last time the item's record was updated.
     */
    function getUnixTimestampByItemName(mysqli $db, string $name) {
        $stmt = $db->prepare("SELECT `timestamp` from `items` WHERE `name` = ?");
        $stmt->bind_param("s", $name);

        $stmt->execute();
        $result = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

        return strtotime($result[0]["timestamp"]);
    }

    /**
     * Updates price data in the database using predownloaded data from the Albion Online Data Project
     * In case of a missing price (0), retains previous price
     * @param mysqli $db database connection
     * @param string $itemName Item name to look up, structured as TX_$type, where X is a number between 2-8
     * @param array $priceData Associative array containing the item's price data
     */
    function updatePrices(mysqli $db, string $itemName, array $priceData) {
        $currentPrices = getItemPricesByName($db, $itemName, false);

        $priceCL = $currentPrices["price_CL"];
        $priceBW = $currentPrices["price_BW"];
        $priceLH = $currentPrices["price_LH"];
        $priceFS = $currentPrices["price_FS"];
        $priceTF = $currentPrices["price_TF"];
        $priceML = $currentPrices["price_ML"];

        foreach($priceData as $city) {
            switch ($city["city"]) {
                case "Caerleon":
                    if($city["sell_price_min"] == 0) break;
                    $priceCL = $city["sell_price_min"];
                    break;
                case "Bridgewatch":
                    if($city["sell_price_min"] == 0) break;
                    $priceBW = $city["sell_price_min"];
                    break;
                case "Lymhurst":
                    if($city["sell_price_min"] == 0) break;
                    $priceLH = $city["sell_price_min"];
                    break;
                case "Fort Sterling":
                    if($city["sell_price_min"] == 0) break;
                    $priceFS = $city["sell_price_min"];
                    break;
                case "Thetford":
                    if($city["sell_price_min"] == 0) break;
                    $priceTF = $city["sell_price_min"];
                    break;
                case "Martlock":
                    if($city["sell_price_min"] == 0) break;
                    $priceML = $city["sell_price_min"];
                    break;
            }
        }
        
        $stmt = $db->prepare("UPDATE `items` SET price_CL = ?, price_BW = ?, price_LH = ?, price_FS = ?, price_TF = ?, price_ML = ?, timestamp = NOW() WHERE `name` = ?");
        $stmt->bind_param("iiiiiis", $priceCL, $priceBW, $priceLH, $priceFS, $priceTF, $priceML, $itemName);

        $stmt->execute();
        $stmt->close();
    }

    /**
     * Fetches new prices for the given item and updates its entry in the database.
     * Should new prices not be available, the previous price for that item will be kept.
     * @param mysqli $db Database connection
     * @param string $itemName Name of the fetched item, structured as TX_$type, where X is a number between 2-8
     */
    function downloadAndUpdatePrices(mysqli $db, string $itemName) {

        $pricesJson = json_decode(JSONfromAOData($itemName), true);
        $currentPrices = getItemPricesByName($db, $itemName, false);

        $priceCL = $currentPrices["price_CL"];
        $priceBW = $currentPrices["price_BW"];
        $priceLH = $currentPrices["price_LH"];
        $priceFS = $currentPrices["price_FS"];
        $priceTF = $currentPrices["price_TF"];
        $priceML = $currentPrices["price_ML"];

        foreach($pricesJson as $city) {
            switch ($city["city"]) {
                case "Caerleon":
                    if($city["sell_price_min"] == 0) break;
                    $priceCL = $city["sell_price_min"];
                    break;
                case "Bridgewatch":
                    if($city["sell_price_min"] == 0) break;
                    $priceBW = $city["sell_price_min"];
                    break;
                case "Lymhurst":
                    if($city["sell_price_min"] == 0) break;
                    $priceLH = $city["sell_price_min"];
                    break;
                case "Fort Sterling":
                    if($city["sell_price_min"] == 0) break;
                    $priceFS = $city["sell_price_min"];
                    break;
                case "Thetford":
                    if($city["sell_price_min"] == 0) break;
                    $priceTF = $city["sell_price_min"];
                    break;
                case "Martlock":
                    if($city["sell_price_min"] == 0) break;
                    $priceML = $city["sell_price_min"];
                    break;
            }
        }
        
        $stmt = $db->prepare("UPDATE `items` SET price_CL = ?, price_BW = ?, price_LH = ?, price_FS = ?, price_TF = ?, price_ML = ?, timestamp = NOW() WHERE `name` = ?");
        $stmt->bind_param("iiiiiis", $priceCL, $priceBW, $priceLH, $priceFS, $priceTF, $priceML, $itemName);

        $stmt->execute();
        $stmt->close();
    }

    /**
     * Takes an associative array formed from a JSON and splits it into smaller arrays sorted by a given field's name
     * @param array $json associative array from a decoded JSON file
     * @param string $fieldName name of the field to split $json by
     * 
     * @return array the processed array
     */
    function splitJsonByField(array $json, string $fieldName) {
        $current = $json[0][$fieldName];
        $output = [$current => []];
        foreach ($json as $key) {
            if($current == $key[$fieldName]) { array_push($output[$current], $key); }
            else {
                $current = $key[$fieldName];
                $output += [$current => []];
                array_push($output[$current], $key);
            }
        }
        return $output;
    }

    /**
     * Fetches all items and their prices that match the given type from the database.
     * @param mysqli $db Database connection
     * @param string $type Item type to look up - possible types: 
     * @param bool $organized if true, will return all items as a processed associative array. All keys will be named "TX_$type", where X is a number from 2-8.
     * @param bool $checkTimestamp if true, will check timestamp on the last price update, and attempt to update if it's outdated.
     * 
     * @return array relevant records from the database
     */
    function getItemsByType(mysqli $db, string $type, bool $organized = true, bool $checkTimestamp = true) {
        $types = typeToResources($type);
        if($checkTimestamp) {
            $stmt = $db->prepare("SELECT `name` FROM `items` WHERE (`type` = ? OR `type` = ?) AND UNIX_TIMESTAMP(NOW())-UNIX_TIMESTAMP(`timestamp`) > 900");
            $stmt->bind_param("ss", $types["RAW"], $types["REFINED"]);

            $stmt->execute();
            $results = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
            $stmt->close();

            $query = "";
            foreach ($results as $key) {
                $query .= $key["name"].",";
            }
            
            $query = substr($query, 0, strlen($query)-1);
            $updateData = json_decode(JSONfromAOData($query), true);

            $updateData = splitJsonByField($updateData, "item_id");

            foreach($results as $key) {
                updatePrices($db, $key["name"], $updateData[$key["name"]]);
            }
        }

        $stmt = $db->prepare("SELECT `name`,`price_CL`,`price_BW`,`price_LH`,`price_FS`,`price_TF`,`price_ML`,`timestamp` FROM `items` WHERE `type` = ? OR `type` = ?");
        $stmt->bind_param("ss", $types["RAW"], $types["REFINED"]);
        
        $stmt->execute();
        $result = $stmt->get_result();

        $results = $result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();

        if(!$organized) return $results;

        $resultsOrganized = [];
        foreach($results as $result) {
            $resultsOrganized[$result["name"]] = array_filter($result, function ($key) { return $key !== "name"; }, ARRAY_FILTER_USE_KEY);
        }
        return $resultsOrganized;
    }

    /**
     * Fetches an item's prices from the database.
     * @param mysqli $db Database connection
     * @param string $name Item name to look up, structured as TX_$type, where X is a number between 2-8
     * @param bool $checkTimestamp if true, will check timestamp on the last price update, and attempt to update if it's outdated.
     * 
     * @return array relevant records from the database
     */
    function getItemPricesByName(mysqli $db, string $name, bool $checkTimestamp = true) {
        global $timeUntilOutdated;
        if($checkTimestamp) {
            if(time() - getUnixTimestampByItemName($db, $name) > $timeUntilOutdated) {
                downloadAndUpdatePrices($db, $name);
            }
        }

        $stmt = $db->prepare("SELECT `price_CL`,`price_BW`,`price_LH`,`price_FS`,`price_TF`,`price_ML` FROM `items` WHERE `name` = ?");
        $stmt->bind_param("s", $name);
        
        $stmt->execute();
        $result = $stmt->get_result();

        $results = $result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();

        return $results[0];
    }

    return $mysqli;
?>