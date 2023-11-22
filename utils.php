<?php 
    $localePath = "assets/locale/items.json";
    $locale = json_decode(file_get_contents($localePath), true);

    $recipesPath = "assets/crafting/refining.json";
    $recipes = json_decode(file_get_contents($recipesPath), true);

    function decodeCity(string $cityCode) {
        switch ($cityCode) {
            case 'price_CL':
                return "Caerleon";
            case 'price_BW':
                return "Bridgewatch";
            case 'price_LH':
                return "Lymhurst";
            case 'price_FS':
                return "FortSterling";
            case 'price_TF':
                return "Thetford";
            case 'price_ML':
                return "Martlock";
            default:
                return "Unknown";
        }
    }

    function getMaxFromAssociativeArray(array $arr, bool $sorted = false) {
        $tempArray = $arr;
        if(isset($tempArray["timestamp"])) unset($tempArray["timestamp"]);
        if(!$sorted) asort($tempArray);
        end($tempArray);
        return [key($tempArray), current($tempArray)];
    }
    function getMinFromAssociativeArray(array $arr, bool $sorted = false) {
        $tempArray = $arr;
        if(isset($tempArray["timestamp"])) unset($tempArray["timestamp"]);
        if(!$sorted) asort($tempArray);
        reset($tempArray);
        return [key($tempArray), current($tempArray)];
    }

    function typeToResources(string $type) {
        $type = strtoupper($type);
        switch ($type) {
            case 'METAL':
                return [
                    "RAW" => "ORE",
                    "REFINED" => "METALBAR"
                ];
            default:
                return "UNKNOWN";
        }
    }

    /**
    *   Display a resource panel, complete with market prices.
    *   @param array $item associative array representing an item, taken from the project's (not API's) database.
    *   @param string $itemName item name, needed for icon and text. structured as TX_$type, where X is a number between 2-8.
    */
    function displayResourcePanel(array $item, string $itemName) {
        global $locale;

        $maxPriceArr = getMaxFromAssociativeArray($item);
        $minPriceArr = getMinFromAssociativeArray($item);
        $maxPrice = $maxPriceArr[1];
        $maxCity = strtolower(decodeCity($maxPriceArr[0]));
        $minPrice = $minPriceArr[1];
        $minCity = strtolower(decodeCity($minPriceArr[0]));

        $itemTitle = $locale[$itemName]["title"];
        $itemSubtitle = $locale[$itemName]["subtitle"];

        $panel = <<<END
        <div class="resource-panel">

        <div class="resource-header">
            <img src="assets/images/{$itemName}.png" alt="selected material">
            <div class="resource-name">
                <h1>{$itemTitle}</h1>
                <p>{$itemSubtitle}</p>
            </div>
        </div>

        <div class="resource-prices">
            <p> Market prices: </p>
            <div class="prices-container">
                <div class="prices-table">
                    <div class="resource-price"><span class="city-label">Martlock:</span><span class="martlock"> {$item["price_ML"]} </span></div>
                    <div class="resource-price"><span class="city-label">Bridgewatch:</span><span class="bridgewatch"> {$item["price_BW"]} </span></div>
                    <div class="resource-price"><span class="city-label">Lymhurst:</span><span class="lymhurst">{$item["price_LH"]}</span></div>
                    <div class="resource-price"><span class="city-label">Fort Sterling:</span><span class="fortsterling">{$item["price_FS"]}</span></div>
                    <div class="resource-price"><span class="city-label">Thetford:</span><span class="thetford">{$item["price_TF"]}</span></div>
                    <div class="resource-price"><span class="city-label">Caerleon:</span><span class="caerleon">{$item["price_CL"]}</span></div>
                </div>
                <div class="divider"></div>
                <div class="prices-highlights">
                    <div class="highlight-panel">
                        <p class="highlight-label lowest">
                            Lowest:
                        </p>
                        <p class="highlight-value {$minCity}"> 
                            {$minPrice}
                        </p>
                    </div>
                    <div class="highlight-panel">
                        <p class="highlight-label highest"> Highest: </p>
                        <p class="highlight-value {$maxCity}">
                            {$maxPrice}
                        </p>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="resource-footer">
            <p class="footer-disclaimer">Last update: </p><span class="footer-timestamp">{$item["timestamp"]} GMT+1</span>
        </div>

        </div>
        END;

        echo $panel;
    }

    function displayCraftingPanel(array $itemType, string $itemName) {
        global $recipes, $locale;
        $ingredient1 = $recipes[$itemName]["INGREDIENT_1"];
        $minIngredient1Arr = getMinFromAssociativeArray($itemType[$ingredient1[0]]);
        $minIngredient1 = $minIngredient1Arr[1];
        $minIngredient1City = strtolower(decodeCity($minIngredient1Arr[0]));

        $complex = false;
        if(isset($recipes[$itemName]["INGREDIENT_2"])) {
            $complex = true;
            $ingredient2 = $recipes[$itemName]["INGREDIENT_2"];
            $minIngredient2Arr = getMinFromAssociativeArray($itemType[$ingredient2[0]]);
            $minIngredient2 = $minIngredient2Arr[1];
            $minIngredient2City = strtolower(decodeCity($minIngredient2Arr[0]));
        }

        $initialCraftingCost = 10*$recipes[$itemName]["NUTRITION"]; 

        $panel = <<<END
        <div class="crafting-panel">
            <h1>Refined with:</h1>
            <div class="crafting-resources">
                <div class="divider-horizontal"></div>
                <div class="crafting-reagents">
                    <div class="crafting-reagent">
                        <div class="reagent-profile">
                            <img src="assets/images/{$ingredient1[0]}.png" alt="crafting reagent">
                            <h2> {$locale[$ingredient1[0]]["title"]} </h2>
                            <h3> {$locale[$ingredient1[0]]["subtitle"]} </h3>
                            <div class="lowest {$minIngredient1City}"> {$minIngredient1} </div>
                        </div>
                        <div class="reagent-amount">
                            <h1> x{$ingredient1[1]} </h1>
                        </div>
                    </div>
        END;
        if($complex) {
            $panel .= <<<END
                <div class="sum">+</div>
                    <div class="crafting-reagent">
                        <div class="reagent-profile">
                            <img src="assets/images/{$ingredient2[0]}.png" alt="crafting reagent">
                            <h2> {$locale[$ingredient2[0]]["title"]} </h2>
                            <h3> {$locale[$ingredient2[0]]["subtitle"]} </h3>
                            <div class="lowest {$minIngredient2City}"> {$minIngredient2} </div>
                        </div>
                        <div class="reagent-amount">
                            <h1> x{$ingredient2[1]} </h1>
                        </div>
                    </div>
                </div>
                <div class="divider-horizontal"></div>
                <div class="crafting-calculations">
                    <div class="nutrition"> 
                        <div class="nutrition-cost">
                            Nutrition required: <span>{$recipes[$itemName]["NUTRITION"]}
                        </div>
                        <div class="station-cost">
                            <p>Enter your station's nutrition cost (per 100 nutrition): <input type="number" name="nutritionCost" min="1" max="9999" value="1000"> </p>
                            <p>Crafting cost: <span class="crafting-cost"> {$initialCraftingCost} </span> silver </p> 
                        </div>
                    </div>

                </div>
            END;
        } else $panel .= "</div>";
        $panel .= <<<END
            </div>
        </div>
        END;

        echo $panel;
    }

    function displayResourceTier(int $tier, string $type, array $itemTypeData) {
        $resourceTypes = typeToResources($type);
        $typeRaw = $resourceTypes["RAW"];
        $typeRefined = $resourceTypes["REFINED"];
        echo "<div class='raw'>";
            displayResourcePanel($itemTypeData["T{$tier}_$typeRaw"], "T{$tier}_$typeRaw");
        echo "</div>";
        echo "<div class='crafting'>";
            displayCraftingPanel($itemTypeData, "T{$tier}_$type");
        echo "</div>";
        echo "<div class='refined'>";
        displayResourcePanel($itemTypeData["T{$tier}_$typeRefined"], "T{$tier}_$typeRefined");
    echo "</div>";
    }
?>