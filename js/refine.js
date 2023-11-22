const tabButtons = document.querySelectorAll(".tab-btn");
const nutritionInputParents = document.querySelectorAll(".station-cost");

var activeTier = 4;

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
    let nutritionCost = nutritionInputParents[index].parentElement.querySelector(".nutrition-cost").querySelector("span").innerText*1;
    inputBox.addEventListener("input", () => {
        if(inputBox.value.length > 4) inputBox.value = inputBox.value.substr(0, 4)*1;
        if(inputBox.value < 1) inputBox.value = 1;
        let costPerNutritionUnit = inputBox.value/100;
        costBox.innerText = Math.round(`${nutritionCost*costPerNutritionUnit}`*10)/10;
        });
}

/**
 * Unhides the initially selected tier once the site loads.
 */
window.addEventListener("load", () => {
    let initiallyActive = document.querySelector(`.tier${activeTier}`);
    initiallyActive.classList.remove("hidden");
});
