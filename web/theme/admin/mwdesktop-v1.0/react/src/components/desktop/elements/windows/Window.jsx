import React, {Component} from 'react';

export class Window extends Component {
    constructor() {
        super();
        this.windowRef = React.createRef();
        this.headerRef = React.createRef();
        this.state = {display: 'block', coordinates: {}, dimensions: {'width':'50%','height':'70%'}, expanded: false};
    }

    componentDidMount() {
        // set initial coordinates
        let windowCoordinates = this.windowRef.current.getBoundingClientRect();
        this.setState(() => ({coordinates: windowCoordinates}));

        let callback = this.dragWindow.bind(this);
        this.headerRef.current.addEventListener('mousedown', (event) => {
            this.mouseCoordinates = {
                top: event.clientY,
                left: event.clientX
            };
            document.addEventListener('mousemove', callback);
        });

        document.addEventListener('mouseup', (event) => {
            document.removeEventListener('mousemove', callback);
        });

        this.headerRef.current.addEventListener('mouseup', (event) => {
            this.expandHalf(event);
        });
    }

    dragWindow(event) {
        // return window to initial state while dragging
        if (this.state.expanded) {
            this.expand(false, true, this.mouseCoordinates);
        }

        let differenceCoordinates = {
            top: event.clientY - this.mouseCoordinates.top,
            left: event.clientX - this.mouseCoordinates.left
        };

        let windowCoordinates = {
            top: parseInt(this.state.coordinates.top) + differenceCoordinates.top,
            left: parseInt(this.state.coordinates.left) + differenceCoordinates.left
        };
        // to prevent moving out of space
        if (windowCoordinates.top <= 0) {
            windowCoordinates.top = 0;
        }
        this.setState(() => ({coordinates: windowCoordinates}));

        this.mouseCoordinates = {
            top: event.clientY,
            left: event.clientX
        }
    }

    minify() {
        this.setState(() => ({display: 'none'}));
    }

    expandHalf(event) {
        // the only exception
        if (this.state.coordinates.top <= 3) {
            this.expand(true);
        }
        if (event.clientX <= 3) {
            this.expand(true, false, {}, 'left');
        }
        if (event.clientX >= document.body.offsetWidth - 3) {
            this.expand(true, false, {}, 'right');
        }
    }

    expand(expand = false, toMouse = false, mouseCoordinates = {}, position = 'center') {
        if (this.state.expanded || !expand) {

            if (toMouse && mouseCoordinates) {
                let windowWidth = parseInt(this.prevDimensions.width);
                let headerHeight = this.headerRef.current.offsetHeight;
                this.prevCoordinates = {
                    top: mouseCoordinates.top - (headerHeight / 2) + 'px',
                    left: mouseCoordinates.left - (windowWidth / 2) + 'px'
                };
            }

            this.setState(() => ({coordinates: this.prevCoordinates, dimensions: this.prevDimensions, expanded: false}));
        } else {
            this.prevCoordinates = this.state.coordinates;
            this.prevDimensions = {
                width: this.windowRef.current.offsetWidth + 'px',
                height: this.windowRef.current.offsetHeight + 'px'
            };
            let newCoordinates = {
                top: 0,
                left: (position === 'left' || position === 'center' ? 0 : (document.body.offsetWidth / 2)),
            };
            let newDimensions = {
                width: (position === 'center' ? '100%' : '50%'),
                height: '100%'
            };

            this.setState(() => ({coordinates: newCoordinates, dimensions: newDimensions, expanded: true}));
        }
    }

    render() {
        return <div ref={this.windowRef}
                    style={{
                        'display': this.state.display,
                        'top': `${this.state.coordinates.top}px`,
                        'left': `${this.state.coordinates.left}px`,
                        'width': `${this.state.dimensions.width}`,
                        'height': `${this.state.dimensions.height}`
                    }}
                    className={'Desktop__Elements__Windows--Window position-fixed fixed-top h-80 theme__background-color3 theme__border theme__border-color' + (this.state.expanded ? ' Desktop__Elements__Windows--Window--expanded' : '')}>
            <div
                className="p-0 theme__background-color2 user-select-none position-relative d-flex justify-content-start flex-row">
                <div ref={this.headerRef}
                     className="p-2 Desktop__Elements__Windows--Window-title">
                    {this.props.title}
                </div>
                <div
                    className="p-0 Desktop__Elements__Windows--Window-controls d-flex justify-content-between flex-row">
                    <div onClick={this.minify.bind(this)} title="Minify"
                         className="p-2 flex-grow-1 controls-minify text-center theme__cursor-pointer theme__background-color--hover-soft">
                        <i className="fas fa-minus"/>
                    </div>
                    <div onClick={() => this.expand(true)} title="Expand"
                         className="p-2 flex-grow-1 controls-fullscreen text-center theme__cursor-pointer theme__background-color--hover-soft">
                        <i className="far fa-window-maximize"/>
                    </div>
                    <div onClick={this.props.onDestroy} title="Close"
                         className="p-2 flex-grow-1 controls-close text-center theme__cursor-pointer theme__background-color--hover">
                        <i className="fas fa-times"/>
                    </div>
                </div>
            </div>
            {this.props.children}
        </div>
    }
}