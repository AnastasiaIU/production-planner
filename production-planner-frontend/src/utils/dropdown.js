/**
 * Initializes the dropdown by exposing the filter function globally and adding necessary event listeners.
 */
async function initDropdown() {
    const dropdownItemsContainer = document.getElementById('dropdownItems');
    const outputsList = document.getElementById('outputsList');
    const form = document.getElementById('planForm');
    const planName = document.getElementById('planName');

    // Expose filter function globally for inline `onkeyup`
    window.filterDropdown = filterDropdown;

    addClearDropdownOnClose();
    addOnClickEventToDropdownItems(dropdownItemsContainer, outputsList, planName);

    // Load a plan if it is provided
    if (typeof plan !== 'undefined') {
        planName.value = plan.display_name;
        for (const [itemId, amount] of Object.entries(plan.items)) {
            const dropdownElement = document.querySelector(`[data-item-id="${itemId}"]`);
            await appendItemToOutputsList(dropdownElement, outputsList, dropdownItemsContainer, planName, parseFloat(amount));
        }
    }

    addEventListenerToPlanForm(form, planName, outputsList);
    addEventListenerToPlanName(planName);
}

/**
 * Adds an event listener to the plan name input field.
 * Resets the custom validity message when the plan name changes.
 *
 * @param {HTMLInputElement} planName The plan name input field.
 */
function addEventListenerToPlanName(planName) {
    planName.addEventListener('change', () => {
        planName.setCustomValidity('');
    });
}

/**
 * Adds an event listener to the form to handle form submission and validate the inputs.
 *
 * @param {HTMLFormElement} form - The production plan form element.
 * @param {HTMLInputElement} planName The plan name input field.
 * @param {HTMLInputElement} outputsList The list of output items in the production graph.
 */
function addEventListenerToPlanForm(form, planName, outputsList) {
    form.addEventListener('submit', function (event) {
        const planNamePrompt = document.getElementById('planNamePrompt');
        const planId = document.getElementById('createPlanId');

        if (typeof plan !== 'undefined') {
            planId.value = plan.id;
        }

        // Reset the plan name validation message
        planNamePrompt.innerHTML = 'Name cannot be empty.';
        planName.setCustomValidity('');

        // Trim the plan name value before submitting
        planName.value = planName.value.trim();
        if (planName.value === '') {
            planName.setCustomValidity('Name cannot be empty.');
            // Stop the form submission
            event.preventDefault();
            event.stopPropagation();
        }

        if (outputsList.children.length === 0 && planName.value !== '') {
            planName.setCustomValidity('Please add at least one item to the plan.');
            planNamePrompt.innerHTML = planName.validationMessage;
            // Stop the form submission
            event.preventDefault();
            event.stopPropagation();
        }

        form.classList.add('was-validated');
    });
}

/**
 * Creates a new list item in the output list with the given item details.
 *
 * @param {string} itemId The ID of the item.
 * @param {string} itemIcon The URL of the item's icon.
 * @param {string} itemName The name of the item.
 * @param {number} amount The amount of the item to be produced.
 * @returns {HTMLElement} The created list item element.
 */
function createListItem(itemId, itemIcon, itemName, amount) {
    const listItem = document.createElement('li');
    listItem.className = 'list-group-item card d-flex flex-column align-items-start border-0 pt-0';
    listItem.innerHTML = `
        <div class='d-flex align-items-center'>
            <img src="${itemIcon}" alt='icon' class="list-item-image">
            <span>${itemName}</span>
            <button type="button" class="btn btn-danger ms-3" aria-label='Remove item' data-item-id="${itemId}">-</button>
        </div>
        <div class='d-flex align-items-center p-0'>
            <input type='number' name="${itemId}" class='form-control text-center quantity-input mt-1' value="${amount}" min='0' step='0.1' aria-label="${itemName} amount" data-item-id="${itemId}">
        </div>
    `;

    return listItem;
}

/**
 * Adds an event listener to the quantity input of a list item to handle changes.
 * Removes the item if the quantity is zero or negative.
 *
 * @param {HTMLElement} listItem The list item element containing the quantity input.
 * @param {HTMLElement} dropdownItemsContainer The container element for the dropdown items.
 * @param {string} itemId The ID of the item.
 */
function addEventListenerToItemQuantity(listItem, dropdownItemsContainer, itemId) {
    const quantityInput = listItem.querySelector(".quantity-input");

    quantityInput.addEventListener('change', async () => {
        let currentValue = parseFloat(quantityInput.value) || 0;

        if (currentValue < 0) currentValue = 0;

        removeProductionGraph(itemId);

        if (currentValue === 0) {
            removeItemFromOutputs(listItem, dropdownItemsContainer, itemId);
        } else {
            await displayProductionGraph(itemId);
        }
    });
}

/**
 * Removes the production graph corresponding to the given item ID.
 *
 * @param {string} itemId The ID of the item.
 */
function removeProductionGraph(itemId) {
    // Remove the corresponding production graph
    const productionGraphContainer = document.getElementById('productionGraph');
    const graphElement = productionGraphContainer.querySelector(`[data-item-id="${itemId}"]`);
    graphElement.remove();
}

/**
 * Removes an item from the output list and shows the corresponding dropdown item.
 *
 * @param {HTMLElement} listItem The list item element to be removed.
 * @param {HTMLElement} dropdownItemsContainer The container element for the dropdown items.
 * @param {string} itemId The ID of the item.
 */
function removeItemFromOutputs(listItem, dropdownItemsContainer, itemId) {
    // Remove the item from outputs
    listItem.remove();

    // Show the corresponding item in the dropdown again
    const dropdownItem = dropdownItemsContainer.querySelector(`[data-item-id="${itemId}"]`);
    changeVisibility(dropdownItem, true);
}

/**
 * Adds an event listener to the remove button of a list item to handle item removal.
 *
 * @param {HTMLElement} listItem The list item element to be removed.
 * @param {HTMLElement} dropdownItemsContainer The container element for the dropdown items.
 * @param {string} itemId The ID of the item.
 */
function addOnClickEventToRemoveBtn(listItem, dropdownItemsContainer, itemId) {
    const removeBtn = listItem.querySelector('.btn-danger');

    removeBtn.addEventListener('click', async () => {
        removeProductionGraph(itemId);
        removeItemFromOutputs(listItem, dropdownItemsContainer, itemId);
    });
}

/**
 * Adds a click event listener to the dropdown items container.
 * Handles the creation of a new list item in the output list and hides the clicked dropdown item.
 *
 */
function addOnClickEventToDropdownItems(dropdownItemsContainer, outputsList, planName) {
    dropdownItemsContainer.addEventListener('click', async (event) => {
        event.preventDefault();

        const target = event.target.closest(".dropdown-item");

        if (target) {
            await appendItemToOutputsList(target, outputsList, dropdownItemsContainer, planName);
        }
    });
}

/**
 * Appends a selected item from the dropdown to the output list and hides the dropdown item.
 *
 * @param {HTMLElement} dropdownElement The dropdown item element that was selected.
 * @param {HTMLElement} outputsList The container element for the output list.
 * @param {HTMLElement} dropdownContainer The container element for the dropdown items.
 * @param {HTMLOutputElement} planName The input for the name of the production plan.
 * @param {number} amount The amount of the item to be produced.
 */
async function appendItemToOutputsList(dropdownElement, outputsList, dropdownContainer, planName, amount = 1) {
    planName.setCustomValidity('');
    const itemId = dropdownElement.dataset.itemId;
    const itemName = dropdownElement.innerText.trim();
    const itemIcon = dropdownElement.querySelector('img')?.src || '';
    const listItem = createListItem(itemId, itemIcon, itemName, amount);

    // Hide the dropdown item
    changeVisibility(dropdownElement, false);

    // Append the new list item to the output list
    outputsList.appendChild(listItem);

    await displayProductionGraph(itemId);

    addEventListenerToItemQuantity(listItem, dropdownContainer, itemId);
    addOnClickEventToRemoveBtn(listItem, dropdownContainer, itemId);
}