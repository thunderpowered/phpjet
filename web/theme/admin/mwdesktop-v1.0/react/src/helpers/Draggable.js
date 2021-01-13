export class Draggable {
    constructor() {
        this.prevCoordinates = {top: 0, left: 0};
        this.elementCoordinates = this.prevCoordinates;
        this.mouseCoordinates = this.prevCoordinates;
    }

    dragElement(event, callback, preventOutOfScreen = true) {
        let differenceCoordinates = {
            top: event.clientY - this.mouseCoordinates.top,
            left: event.clientX - this.mouseCoordinates.left
        };

        let elementCoordinates = {
            top: this.elementCoordinates.top + differenceCoordinates.top,
            left: this.elementCoordinates.left + differenceCoordinates.left
        };

        if (preventOutOfScreen && elementCoordinates.top <= 0) {
            elementCoordinates.top = 0;
            // i feel like i should do the same for left, right and bottom
        }

        console.log({top: event.clientY, left: event.clientX});
        console.log(elementCoordinates);

        callback(elementCoordinates);
        this.elementCoordinates = elementCoordinates;
        this.mouseCoordinates = {top: event.clientY, left: event.clientX};
    }
}