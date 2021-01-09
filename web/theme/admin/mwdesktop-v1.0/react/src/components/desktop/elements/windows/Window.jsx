import React, {Component} from 'react';
import {SimpleLoader} from "../loaders/SimpleLoader";

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
            // draggable
            coordinates: {},
            dimensions: {'width': document.body.offsetWidth / 2, 'height': document.body.offsetHeight / 1.2},
            expanded: true,
            expandClass: 'Desktop__Elements__Windows--Window--expand-right',
            // 'loaded' represents whether or not window is visible
            // i call it loaded because it depends on children component
            // when it loaded it should call this.props.onLoaded() to tell Window that it actually done loading
            // actually the only purpose of this -> to show the loading spinner when it's not loaded
            loaded: false,
            // 'active' represents whether or not window it is in memory
            // if window is inactive for some time -> we disable it
            // some kind of optimization
            // the default is of course 'true'
            active: true,
            // child key is an index of the child
            // to force remounting of children we should change the key
            // we actually don't need it when disabling window due to time, since it completely removed from DOM
            // if if we have to refresh it, it could be useful
            // but i use it in both cases, nothing wrong with it
            childrenKey: 1
        };

        this.windowLifeSpan = 300000; // 5 min

        // callbacks
        this.resizeCallback = () => {
        };
        this.mouseMoveCallback = () => {
        };
        this.mouseUpCallback = () => {
        };
        this.removeMouseupCallback = () => {
        };
        this.mouseDownCallbackDocument = () => {
        };
        this.mouseDownCallbackHeader = () => {
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
        this.resizeCallback = (event) => this.resize(event, this.direction);
        [this.resizeLeft, this.resizeRight, this.resizeBottom].forEach((ref) => {
            ref.current.addEventListener('mousedown', () => {
                document.addEventListener('mousemove', this.resizeCallback);
            });
        });

        // make it draggable, very simple
        this.mouseMoveCallback = this.dragWindow.bind(this);
        this.mouseUpCallback = (event) => {
            this.expandHalf(event);
        };
        this.mouseDownCallbackHeader = (event) => {
            if (event.button !== 0) return;
            document.addEventListener('mousemove', this.mouseMoveCallback);
            this.headerRef.current.addEventListener('mouseup', this.mouseUpCallback);
        };

        this.headerRef.current.addEventListener('mousedown', this.mouseDownCallbackHeader);

        this.mouseDownCallbackDocument = (event) => {
            this.mouseCoordinates = {top: event.clientY, left: event.clientX};
        };
        document.addEventListener('mousedown', this.mouseDownCallbackDocument);

        // remove all callbacks
        this.removeMouseupCallback = () => {
            document.removeEventListener('mousemove', this.mouseMoveCallback);
            document.removeEventListener('mousemove', this.resizeCallback);
            this.headerRef.current.removeEventListener('mouseup', this.mouseUpCallback);
        };
        document.addEventListener('mouseup', this.removeMouseupCallback);
    }

    componentDidUpdate(prevProps) {
        if (!this.state.loaded && prevProps.visible !== this.props.visible && this.props.visible) {
            this.refreshWindow();
        }
        if (!this.props.visible) {
            // if window was hidden -> start counter to its death
            this.windowCreated = (new Date()).getTime();
            this.windowAliveInterval = setInterval(() => {
                this.shouldThisWindowBeDisabled()
            }, 10000); // check every ten seconds
        } else {
            clearInterval(this.windowAliveInterval);
        }
    }

    componentWillUnmount() {
        this.headerRef.current.removeEventListener('mouseup', this.mouseUpCallback);
        this.headerRef.current.removeEventListener('mousedown', this.mouseDownCallbackHeader);
        document.removeEventListener('mousedown', this.mouseDownCallbackDocument);
        document.removeEventListener('mouseup', this.removeMouseupCallback);
        document.removeEventListener('mousemove', this.resizeCallback);
    }

    refreshWindow() {
        this.setState(() => ({
            childrenKey: ++this.state.childrenKey,
            loaded: false,
            active: true
        }));
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

    onLoaded(callBack) {
        this.setState(() => ({loaded: true, active: true}), callBack);
    }

    shouldThisWindowBeDisabled() {
        let currentTime = (new Date()).getTime();
        if (currentTime - this.windowCreated > this.windowLifeSpan && !this.props.visible && this.state.loaded) {
            this.setState(() => ({loaded: false, active: false}));
        }
    }

    render() {
        // don't confuse it with state.active. It is not the same!
        // it represents whether or not window minified, it can be active, but not visible
        // it can be active and visible, but not loaded and so on
        let display = this.props.visible ? 'block' : 'none';
        return <div ref={this.windowRef}
                    style={{
                        'display': display,
                        'top': `${this.state.coordinates.top}px`,
                        'left': `${this.state.coordinates.left}px`,
                        'width': `${this.state.dimensions.width}px`,
                        'height': `${this.state.dimensions.height}px`,
                        'z-index': `${this.props.index + 4}` // set it above others
                    }}
                    onMouseDown={() => {
                        this.sortWindows(this.props.index)
                    }}
                    className={'Desktop__Elements__Windows--Window overflow-auto position-fixed d-flex flex-column fixed-top h-80 theme__background-color3 theme__border theme__border-color display-' + display + (this.state.expanded ? ' Desktop__Elements__Windows--Window--expanded' : '') + ' ' + this.state.expandClass}>
            <div
                className="p-0 theme__background-color2 user-select-none position-relative d-flex justify-content-start flex-row">

                {/* Header is not only for showing title, we also use it for activate gragging */}
                <div ref={this.headerRef}
                     className="p-2 Desktop__Elements__Windows--Window-title">
                    {this.props.title}
                </div>

                <div
                    className="p-0 Desktop__Elements__Windows--Window-controls d-flex justify-content-between flex-row">

                    {/* Window controls */}
                    <div onClick={() => {
                        this.props.onMinifyWindow(this.props.index)
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
            <div className="p-2 Desktop__Elements__Windows--Window-content overflow-auto">
                {this.state.active &&
                <div
                    className={`m-2 Desktop__Elements__Windows--Window-content-inner position-relative ${this.state.loaded ? 'd-block' : 'd-none'}`}>
                    {React.Children.map(this.props.children, child => (
                        React.cloneElement(child, {
                            ...child.props,
                            onLoaded: this.onLoaded.bind(this),
                            openChildWindow: this.props.openChildWindow,
                            parent: this.props.parent,
                            key: this.state.childrenKey,
                            windowData: this.props.windowData
                        })
                    ))}
                </div>
                }
                {!this.state.loaded &&
                <SimpleLoader/>
                }
            </div>

            {/* Resizing controls */}
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