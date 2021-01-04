import React, {Component} from 'react';

export class Window extends Component {
    constructor() {
        super();
        this.windowRef = React.createRef();
        this.headerRef = React.createRef();
        this.state = {display: 'block', coordinates: {}};
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
    }

    dragWindow(event) {
        let differenceCoordinates = {
            top: event.clientY - this.mouseCoordinates.top,
            left: event.clientX - this.mouseCoordinates.left
        };

        let windowCoordinates = {
            top: this.state.coordinates.top + differenceCoordinates.top,
            left: this.state.coordinates.left + differenceCoordinates.left
        };
        this.setState(() => ({coordinates: windowCoordinates}));

        this.mouseCoordinates = {
            top: event.clientY,
            left: event.clientX
        }
    }


    minify() {
        this.setState(() => ({display: 'none'}));
    }

    render() {
        return <div ref={this.windowRef}
                    style={{
                        'display': this.state.display,
                        'top': `${this.state.coordinates.top}px`,
                        'left': `${this.state.coordinates.left}px`
                    }}
                    className={'Desktop__Elements__Windows--Window position-fixed fixed-top w-50 h-70 theme__background-color3 theme__border theme__border-color js-plugin_draggable'}>
            <div
                className="p-0 theme__background-color2 user-select-none position-relative d-flex justify-content-start flex-row">
                <div ref={this.headerRef}
                     className="p-2 Desktop__Elements__Windows--Window-title js-plugin_draggable__handle">
                    {this.props.title}
                </div>
                <div
                    className="p-0 Desktop__Elements__Windows--Window-controls d-flex justify-content-between flex-row">
                    <div onClick={this.minify.bind(this)} title="Minify"
                         className="p-2 flex-grow-1 controls-minify text-center theme__cursor-pointer theme__background-color--hover-soft">
                        <i className="fas fa-minus"/>
                    </div>
                    <div title="Expand"
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