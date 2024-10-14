import { Controller } from '@hotwired/stimulus';

/*
 * This is an example Stimulus controller!
 *
 * Any element with a data-controller="hello" attribute will cause
 * this controller to be executed. The name "hello" comes from the filename:
 * hello_controller.js -> "hello"
 *
 * Delete this file or adapt it for your use!
 */

export default class extends Controller {
    connect() {
        this.index = this.addElement.childElementCount
       const btn = document.createElement('button')
       btn.setAttribute('class', 'btn-secondary')
       btn.innerText = 'Ajouter un element'
       btn.setAttribute('type', 'button')
       btn.addEventListener('click', this.addEventListener)     
       this.addElement.append(btn)   
       this.element.chilNodes.forEach(this.addDeleteButton)
    }
    /**
     * 
     * @param {MouseEvent} e 
     */

    addElement = (e) => {
        e.preventDefault()
        const element = document.createRange().createContextualFragment(
            this.element.dataset['prototype'].replaceAll('__name__',this.ndex)
        ).firstElementChild
        this.addDeleteButton(element)
        this.index++
        e.currentTarget.insertAdjaccentElement('beforebegin', element)
    }
    /**
     * @param {HTMLElement} item
     */
    addDeleteButton = (item) => {
        const btn = document.createElement('button')
       btn.setAttribute('class', 'btn-secondary')
       btn.innerText = 'Supprimer'
       btn.setAttribute('type', 'button')
       item.append(btn)
       btn.addEventListener('click', e => {
        e.preventDefault()
        item.remove()
       })
    }
}
