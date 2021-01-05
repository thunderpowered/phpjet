import React, {Component} from 'react';

export class Window extends Component {
    constructor(props) {
        super(props);
        this.windowRef = React.createRef();
        this.headerRef = React.createRef();

        this.resizeLeft = React.createRef();
        this.resizeRight = React.createRef();
        this.resizeBottom = React.createRef();
        this.direction = '';

        this.prevCoordinates = {top: 0, left: 0};
        this.state = {
            coordinates: {},
            dimensions: {'width': document.body.offsetWidth / 2, 'height': document.body.offsetHeight / 1.2},
            expanded: true,
            expandClass: 'Desktop__Elements__Windows--Window--expand-right'
        };
    }

    componentDidMount() {
        // set initial coordinates
        let windowCoordinates = this.windowRef.current.getBoundingClientRect();
        this.setState(() => ({coordinates: windowCoordinates}));

        // and dimensions
        this.prevDimensions = {
            width: this.windowRef.current.offsetWidth,
            height: this.windowRef.current.offsetHeight
        };

        // add resizing
        let resizeCallback = (event) => this.resize(event, this.direction);
        [this.resizeLeft, this.resizeRight, this.resizeBottom].forEach((ref) => {
            ref.current.addEventListener('mousedown', () => {
                document.addEventListener('mousemove', resizeCallback);
            });
        });

        // make it draggable, very simple
        let callback = this.dragWindow.bind(this);
        this.headerRef.current.addEventListener('mousedown', () => {
            document.addEventListener('mousemove', callback);
        });

        document.addEventListener('mousedown', (event) => {
            this.mouseCoordinates = {top: event.clientY, left: event.clientX};
        });

        // remove all callbacks
        document.addEventListener('mouseup', (event) => {
            document.removeEventListener('mousemove', callback);
            document.removeEventListener('mousemove', resizeCallback);
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
            top: this.state.coordinates.top + differenceCoordinates.top,
            left: this.state.coordinates.left + differenceCoordinates.left
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

    // not very good, but ok
    expandHalf(event) {
        // the only exception
        if (this.state.coordinates.top <= 5) {
            this.expand(true);
        }
        if (event.clientX <= 5) {
            this.expand(true, false, {}, 'left');
        }
        if (event.clientX >= document.body.offsetWidth - 5) {
            this.expand(true, false, {}, 'right');
        }
    }

    expand(expand = false, toMouse = false, mouseCoordinates = {}, position = 'center') {
        if (this.state.expanded || !expand) {

            // set window to mouse coordinates, it's much more convenient this way
            if (toMouse && mouseCoordinates) {
                let windowWidth = this.prevDimensions.width;
                let headerHeight = this.headerRef.current.offsetHeight;
                this.prevCoordinates = {
                    top: mouseCoordinates.top - (headerHeight / 2),
                    left: mouseCoordinates.left - (windowWidth / 2)
                };
            }

            this.setState(() => ({coordinates: this.prevCoordinates, expanded: false, expandClass: ''}));
        } else {
            this.prevDimensions = {
                width: this.windowRef.current.offsetWidth,
                height: this.windowRef.current.offsetHeight
            };
            // Just using classes
            this.setState(() => ({
                expanded: true,
                expandClass: 'Desktop__Elements__Windows--Window--expand-' + position
            }));
        }
    }

    sortWindows(index) {
        this.props.onSortWindows(index);
    }

    resize(event, direction) {
        let differenceCoordinates = {
            top: event.clientY - this.mouseCoordinates.top,
            left: event.clientX - this.mouseCoordinates.left
        };

        // well it's not direction, it is just side of the window, i should've named it differently, but who cares?
        if (direction === 'left') {
            // in this case we need to change width and move to the left/right by this change
            let newWidth = this.state.dimensions.width - differenceCoordinates.left;
            let newLeft = this.state.coordinates.left + differenceCoordinates.left;
            this.setState(() => ({
                dimensions: {...this.state.dimensions, width: newWidth},
                coordinates: {...this.state.coordinates, left: newLeft}
            }));
        } else if (direction === 'bottom') {
            // just change width and height...
            let newHeight = this.state.dimensions.height + differenceCoordinates.top;
            this.setState(() => ({dimensions: {...this.state.dimensions, height: newHeight}}));
        } else if (direction === 'right') {
            let newWidth = this.state.dimensions.width + differenceCoordinates.left;
            this.setState(() => ({dimensions: {...this.state.dimensions, width: newWidth}}));
        }

        this.mouseCoordinates = {top: event.clientY, left: event.clientX};
    }

    render() {
        let display = this.props.active ? 'block' : 'none';
        return <div ref={this.windowRef}
                    style={{
                        'display': display,
                        'top': `${this.state.coordinates.top}px`,
                        'left': `${this.state.coordinates.left}px`,
                        'width': `${this.state.dimensions.width}px`,
                        'height': `${this.state.dimensions.height}px`,
                        'z-index': `${this.props.zIndex + 4}` // set it above others
                    }}
                    onMouseDown={() => {
                        this.sortWindows(this.props.configIndex)
                    }}
                    className={'Desktop__Elements__Windows--Window position-fixed fixed-top h-80 theme__background-color3 theme__border theme__border-color' + (this.state.expanded ? ' Desktop__Elements__Windows--Window--expanded' : '') + ' ' + this.state.expandClass}>
            <div
                className="p-0 theme__background-color2 user-select-none position-relative d-flex justify-content-start flex-row">
                <div ref={this.headerRef}
                     className="p-2 Desktop__Elements__Windows--Window-title">
                    {this.props.title}
                </div>
                <div
                    className="p-0 Desktop__Elements__Windows--Window-controls d-flex justify-content-between flex-row">
                    <div onClick={() => {
                        this.props.onClickWindows(this.props.index)
                    }} title="Minify"
                         className="p-2 flex-grow-1 controls-minify text-center theme__cursor-pointer theme__background-color--hover-soft">
                        <i className="fas fa-minus"/>
                    </div>
                    <div onClick={() => this.expand(true)} title="Expand"
                         className="p-2 flex-grow-1 controls-fullscreen text-center theme__cursor-pointer theme__background-color--hover-soft">
                        <i className="far fa-window-maximize"/>
                    </div>
                    <div onClick={(e) => this.props.onDestroy(this.props.index, this.props.configIndex, e)}
                         title="Close"
                         className="p-2 flex-grow-1 controls-close text-center theme__cursor-pointer theme__background-color--hover">
                        <i className="fas fa-times"/>
                    </div>
                </div>
            </div>
            <div className="p-2 Desktop__Elements__Windows--Window-content">
                <div className="m-2 Desktop__Elements__Windows--Window-content-inner overflow-auto">
                    {this.props.children}
                </div>
            </div>

            {/* resizing controls */}
            <div className="window-resize--left" ref={this.resizeLeft} onMouseDown={() => {
                this.direction = 'left'
            }}/>
            <div className="window-resize--bottom" ref={this.resizeBottom} onMouseDown={() => {
                this.direction = 'bottom'
            }}/>
            <div className="window-resize--right" ref={this.resizeRight} onMouseDown={() => {
                this.direction = 'right'
            }}/>
        </div>
    }
}