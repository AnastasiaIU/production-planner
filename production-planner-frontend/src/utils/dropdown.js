import { Graph } from "@/utils/Graph.js"

export class Dropdown {
    constructor() {
        this.graph = new Graph()
    }

    getItemVisibility(item, searchValue) {
        const text = item.textContent || item.innerText
        const matchesFilter = text.toLowerCase().includes(searchValue.toLowerCase())
        const isInOutputList = item.querySelector(".dropdown-item").style.display === 'none'
        const isVisible = matchesFilter && !isInOutputList

        this.changeVisibility(item, isVisible)

        return isVisible
    }

    /**
     * Changes the visibility of an element based on the specified condition.
     *
     * @param {HTMLElement} element The element whose visibility is to be changed.
     * @param {boolean} isVisible Indicates if the element should be visible or hidden.
     */
    changeVisibility(element, isVisible) {
        if (isVisible) {
            element.classList.remove('hide-element')
            element.classList.add('show-element')
        } else {
            element.classList.remove('show-element')
            element.classList.add('hide-element')
        }
    }

    /**
    * Retrieves all items under a given category element.
    *
    * @param {HTMLElement} category The category element whose items are to be retrieved.
    * @returns {HTMLElement[]} An array of item elements under the specified category.
    */
    getCategoryItems(category) {
        const categoryItems = []
        let nextElement = category.nextElementSibling

        // Collect all items under this category
        while (nextElement && nextElement.tagName === 'LI') {
            categoryItems.push(nextElement)
            nextElement = nextElement.nextElementSibling
        }

        return categoryItems
    }

    async appendItemToOutputsList(element, amount = 1) {
        const planName = document.getElementById('planName')
        planName.setCustomValidity('')

        const itemId = element.dataset.itemId
        const itemName = element.innerText.trim()
        const itemIcon = element.querySelector('img')?.src || ''
        const listItem = this.createListItem(itemId, itemIcon, itemName, amount)

        // Hide the dropdown item
        this.changeVisibility(element, false)

        const outputsList = document.getElementById('outputsList')
        outputsList.appendChild(listItem)

        await this.graph.displayGraph(itemId)

        const dropdownContainer = document.getElementById('dropdownItems')

        const quantityInput = listItem.querySelector(".quantity-input")
        quantityInput.addEventListener('change', async event => {
            this.handleQuantityChange(event, dropdownContainer, itemId)
        })

        const removeBtn = listItem.querySelector('.btn-danger')
        removeBtn.addEventListener('click', async () => {
            this.graph.removeGraph(itemId)
            this.removeItemFromOutputs(listItem, dropdownContainer, itemId)
        });
    }

    async handleQuantityChange(event, dropdownContainer, itemId) {
        let currentValue = parseFloat(event.target.value) || 0

        if (currentValue < 0) currentValue = 0

        this.graph.removeGraph(itemId)

        if (currentValue === 0) {
            this.removeItemFromOutputs(event, dropdownContainer, itemId);
        } else {
            await this.graph.displayGraph(itemId)
        }
    }

    /**
    * Removes an item from the output list and shows the corresponding dropdown item.
    *
    * @param {HTMLElement} listItem The list item element to be removed.
    * @param {HTMLElement} dropdownContainer The container element for the dropdown items.
    * @param {string} itemId The ID of the item.
    */
    removeItemFromOutputs(listItem, dropdownContainer, itemId) {
        // Remove the item from outputs
        listItem.remove();

        // Show the corresponding item in the dropdown again
        const dropdownItem = dropdownContainer.querySelector(`[data-item-id="${itemId}"]`);
        this.changeVisibility(dropdownItem, true);
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
    createListItem(itemId, itemIcon, itemName, amount) {
        const listItem = document.createElement('li');
        listItem.className = 'list-group-item card d-flex flex-column align-items-start border-0 pt-0';
        listItem.innerHTML = `<div class='d-flex align-items-center'>
            <img src="${itemIcon}" alt='icon' class="list-item-image">
            <span>${itemName}</span>
            <button type="button" class="btn btn-danger ms-3" aria-label='Remove item' data-item-id="${itemId}">-</button>
        </div>
        <div class='d-flex align-items-center p-0'>
            <input type='number' name="${itemId}" class='form-control text-center quantity-input mt-1' value="${amount}" min='0' step='0.1' aria-label="${itemName} amount" data-item-id="${itemId}">
        </div>`;

        return listItem;
    }
}