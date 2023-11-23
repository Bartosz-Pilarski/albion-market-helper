<?php
        require "requests.php";
        require "database.php";
        require "utils.php";

        $type = $_GET["type"];

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/refine.css">
    <script defer src="js/refine.js"></script>
    <title>Albion Market Helper</title>
</head>
<body>
    <span style="display: none;" id="type"><?php echo $type; ?></span>
    <header><a href="index.php"><h1>Albion.market</h1></a> <span>Economy utility for Albion Online</span></header>
    <nav><a href="refine.php?type=METAL">Metal</a> <div class="divider"></div><a href="refine.php?type=FIBER">Cloth</a> <div class="divider"></div><a href="refine.php?type=WOOD">Wood</a> <div class="divider"></div><a href="refine.php?type=LEATHER">Leather</a> <div class="divider"></div><a href="refine.php?type=STONE">Stone</a></nav>
    <div id="main-container">
        <div id="tabs">
            <div id="material-indicator"><?php echo $type ?></div>
            <div id="tier-indicator">Tier:</div>
            <button data-tier="2" class="tab-btn">II</button>
            <button data-tier="3" class="tab-btn">III</button>
            <button data-tier="4" class="tab-btn tab-active">IV</button>
            <button data-tier="5" class="tab-btn">V</button>
            <button data-tier="6" class="tab-btn">VI</button>
            <button data-tier="7" class="tab-btn">VII</button>
            <button data-tier="8" class="tab-btn">VIII</button>
        </div>
        <main>
            <?php 
                $allMetals = getItemsByType($mysqli, $type);
                for ($i=2; $i < 9; $i++) { 
                    echo "<div class='refining-tier tier$i hidden'>";
                        displayResourceTier($i, $type, $allMetals);
                    echo "</div>";
                }
            ?>
            <div class="divider-horizontal"></div>
            <div class="refining-calculator">
                <div class="refining-header">
                    <div class="silver"> <div class="silver-coin">1</div> </div>
                    <h1>Refining calculator:</h1>
                </div>
            </div>
        </main>
    </div>

</body>
</html>