<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/index.css">
    <title>Albion Market Helper</title>
</head>
<body>
    <header><a href="index.php"><h1>Albion.market</h1></a> <span>Economy utility for Albion Online</span></header>
    <nav><a href="refine.php?type=METAL">Metal</a> <div class="divider"></div><a href="refine.php?type=FIBER">Cloth</a> <div class="divider"></div><a href="refine.php?type=WOOD">Wood</a> <div class="divider"></div><a href="refine.php?type=LEATHER">Leather</a> <div class="divider"></div><a href="refine.php?type=BLOCK">Stone</a></nav>
    <main>
        <div id="main-body">
            <h1>Welcome to the Albion Market Helper!</h1>
            <div class="divider-horizontal"></div>
            <p>
                This tool is designed to help Albion Online players participate in the economy of the game by making it easier to find, compare and calculate prices of goods on the market.
            </p>
            <div class="divider-horizontal"></div>
            <p>
                The market prices are contributed by the community and supplied by the <a href="https://www.albion-online-data.com/" target="_blank">Albion Online Data Project</a>, and are not guaranteed to be always 100% accurate. They will, however, generally provide a good approximation. 
            </p>
            <div class="divider-horizontal"></div>
            <p>
                Currently, only the Albion West server is supported. Prices shown here do not reflect those of the Albion East server.
            </p>
            <div class="divider-horizontal"></div>
            <p>
                This project is <a href="https://github.com/Bartosz-Pilarski/albion-market-helper" target="_blank">open source</a>!
            </p>
            <div id="footer"> &copy;2023, Bartosz Pilarski</div>
        </div>
        <div id="categories">
            <h2>Pick the material you wish to refine:</h2>
            <div class="divider-horizontal"></div>
            <ul>
                <a href="refine.php?type=METAL">
                    <li>
                        <img src="assets/images/T4_METALBAR.png" alt="a bar of metal">
                        <div class="divider"></div>
                        <span><b>Metal</b></span>
                    </li>
                </a>
                <a href="refine.php?type=FIBER">
                    <li>
                        <img src="assets/images/T4_CLOTH.png" alt="a roll of cloth">
                        <div class="divider"></div>
                        <span><b>Fiber</b></span>
                    </li>
                </a>
                <a href="refine.php?type=WOOD">
                    <li>
                        <img src="assets/images/T4_PLANKS.png" alt="a plank of wood">
                        <div class="divider"></div>
                        <span><b>Wood</b></span>
                    </li>
                </a>
                <a href="refine.php?type=LEATHER">
                    <li>
                        <img src="assets/images/T4_LEATHER.png" alt="a piece of leather">
                        <div class="divider"></div>
                        <span><b>Leather</b></span>
                    </li>
                </a>
                <a href="refine.php?type=STONE">
                    <li>
                        <img src="assets/images/T4_STONEBLOCK.png" alt="a block of stone">
                        <div class="divider"></div>
                        <span><b>Stone</b></span>
                    </li>
                </a>
            </ul>
        </div>
    </main>
</body>
</html>