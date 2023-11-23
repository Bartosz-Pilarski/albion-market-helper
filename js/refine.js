const tabButtons = document.querySelectorAll(".tab-btn");
const nutritionInputParents = document.querySelectorAll(".station-cost");
const logBox = document.querySelector("#log");

var activeTier = 4;

//For the refining calculator
const rawInputBox = document.querySelector("#rawInput");
const refinedOutputBox = document.querySelector("#refinedOutput");
var nutritionCost;
//Will store ingredients as array ingredient [COST, AMOUNT]
var ingredients = [];

/** 
 * Tab functionality. Adds an event listener to every tab that hides all irrelevant material tiers and then unhides the one the user is requesting.
*/
for (let index = 0; index < tabButtons.length; index++) {
    tabButtons[index].addEventListener("click", () => {

        let tabTier = tabButtons[index].dataset.tier;
        if(tabTier == activeTier) return;
        document.querySelector(".tab-active").classList.remove("tab-active");
        let previousActive = document.querySelector(`.tier${activeTier}`);
        previousActive.classList.add("hidden");

        activeTier = tabTier;
        tabButtons[index].classList.add("tab-active");
        let nowActive = document.querySelector(`.tier${activeTier}`);
        nowActive.classList.remove("hidden");
    })
}

/**
 * Nutrition cost functionality, handles user input and calculates crafting cost for each material
 * @TODO sync nutrition cost across all materials and calculate all of them simultaneously
 */
for (let index = 0; index < nutritionInputParents.length; index++) {
    let inputBox = nutritionInputParents[index].children[0].children[0];
    let costBox = nutritionInputParents[index].children[1].children[0];
    nutritionCost = nutritionInputParents[index].parentElement.querySelector(".nutrition-cost").querySelector("span").innerText*1;
    inputBox.addEventListener("input", () => {
        if(inputBox.value.length > 4) inputBox.value = inputBox.value.substring(0, 4)*1;
        if(inputBox.value < 1) inputBox.value = 1;
        let costPerNutritionUnit = inputBox.value/100;
        nutritionCost = Math.round(`${nutritionCost*costPerNutritionUnit}`*10)/10;
        costBox.innerText = nutritionCost;
        });
}

function calculateRefiningFromRaw(amount) {
    let output = "";
    logBox.innerText = "";
    
    let reagentPerUnit = ingredients[1][1]/ingredients[0][1];
    let roundedReagentsNeeded = Math.floor(amount*reagentPerUnit);

    let rawCost = ingredients[0][0]*amount;
    let reagentCost = ingredients[1][0]*roundedReagentsNeeded;
    let totalCost = rawCost+reagentCost;

    let craftingOutput = Math.floor(amount/ingredients[0][1]);
    
    output += `Raw: ${ingredients[0][0]}x${amount} `;
    if(typeof ingredients[1] !== undefined) {
        output += `Reagent: ${ingredients[1][0]}x${roundedReagentsNeeded} `;
    }
    output += `Total initial investment: ${totalCost} `;
    output += `Refined Output: ${craftingOutput}`;

    
    logBox.innerText = output;
}

/**
 * Unhides the initially selected tier once the site loads.
 */
window.addEventListener("load", () => {
    //Unhide active
    let initiallyActive = document.querySelector(`.tier${activeTier}`);
    initiallyActive.classList.remove("hidden");

    //Set up initial refining calculation
    let reagents = initiallyActive.querySelectorAll(".crafting-reagent");
    for (let index = 0; index < reagents.length; index++) {
        ingredients.push([reagents[index].querySelector(".lowest").innerText]);
        ingredients[index].push(reagents[index].querySelector(".reagent-amount").querySelector("h1").innerText.substring(1))
        
        logBox.innerText += " "+ingredients[index][0]+"x"+ingredients[index][1];
    }
    nutritionCost = initiallyActive.querySelector(".crafting-cost").innerText*1
    logBox.innerText += " "+nutritionCost;

    //set up user input for the refining calculator
    rawInputBox.addEventListener("input", () => {
        if(rawInputBox.value < 1) rawInputBox.value = 1;
        calculateRefiningFromRaw(rawInputBox.value);
    })
});
