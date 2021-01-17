import React from "react";

export class Chunk extends React.Component {
    constructor() {
        super();
        this.state = {
            inPlace: false
        };
    }

    componentDidMount() {
        if (typeof this.props.inPlace !== 'undefined' && this.state.inPlace !== this.props.inPlace) {
            this.setState(() => ({inPlace: this.props.inPlace}))
        }
    }

    render() {
        if (this.props.hidden) {
            return <div />
        }

        // inPlace === true and inPlace === false have different appearance
        // but it is temporary, i want to render items the way it will be rendered on page
        return <div ref={this.props.passRef} className={`PageBuilder__elements-item-container p-3 ${this.state.inPlace ? 'w-100' : 'pt-0 pb-0'} ${this.props.hidden ? 'd-none' : ''}`} style={this.props.style}>
            <div onMouseDown={event => this.props.onDrugChunk(event, this.props.index)}
                 style={{top: this.props.coordinates.top, left: this.props.coordinates.left}}
                 className={`PageBuilder__elements-item p-3 user-select-none theme__cursor-pointer theme__background-color3 theme__background-color--hover-soft theme__background-color--active-soft ${this.state.inPlace ? 'w-100' : ''}`}>
                <div className="p-3 text-center mt-2">
                    <i className="fas fa-puzzle-piece fs-4"/>
                </div>
                <div className="text-center text-wrap">
                    {this.props.name}
                </div>
            </div>
        </div>
    }
}