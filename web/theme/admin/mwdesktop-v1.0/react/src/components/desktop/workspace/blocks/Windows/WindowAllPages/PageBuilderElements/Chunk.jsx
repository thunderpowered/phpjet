import React from "react";
import {Formatter} from "../../../../../../../helpers/Formatter";

export class Chunk extends React.Component {
    constructor() {
        super();
        this.state = {
            inPlace: false,
            params: {}
        };
        this.formatter = new Formatter();
    }

    componentDidMount() {
        if (typeof this.props.inPlace !== 'undefined' && this.state.inPlace !== this.props.inPlace) {
            this.setState(() => ({inPlace: this.props.inPlace}))
        }

        // it is easy to handle it if we add it to the state
        this.setState(() => ({
            params: this.props.params
        }));
    }

    onChange(event, paramName, chunkIndex, eventValue = 'checked') {
        this.setState(() => ({
            params: {
                ...this.state.params,
                [paramName]: event.target[eventValue]
            }
        }), () => this.updatePageStructure());
    }

    updatePageStructure() {
        this.props.onChangeParams(this.props.identifier, this.state.params);
    }

    onMouseDown(event) {
        // prevent blocks from accidental moving when user interacts with inputs
        event.stopPropagation();
    }

    render() {
        if (this.props.hidden) {
            return <div />
        }

        // inPlace === true and inPlace === false have different appearance
        // but it is temporary, i want to render items the way it will be rendered on page
        return <div ref={this.props.passRef} className={`PageBuilder__elements-item-container p-3 ${this.state.inPlace ? 'w-100' : 'pt-0 pb-0 PageBuilder__elements-item--inPlace'} ${this.props.hidden ? 'd-none' : ''}`} style={this.props.style}>
            <div onMouseDown={event => this.props.onDrugChunk(event, this.props.index)}
                 style={{top: this.props.coordinates.top, left: this.props.coordinates.left}}
                 className={`PageBuilder__elements-item p-3 user-select-none theme__cursor-pointer theme__background-color3 theme__background-color--hover-soft theme__background-color--active-soft ${this.state.inPlace ? 'w-100 d-flex flex-row justify-content-start align-items-center' : ''}`}>
                <div className={`p-3 text-center ${this.state.inPlace ? '' : 'mt-2'}`}>
                    <i className="fas fa-puzzle-piece fs-4"/>
                </div>
                <div className={`text-center text-wrap ${this.state.inPlace ? 'p-3' : ''}`}>
                    {this.props.name}
                </div>
                {this.state.inPlace &&
                    <div onMouseDown={(e) => this.onMouseDown(e)} className="d-flex flex-row justify-content-start align-items-center">
                        {Object.keys(this.state.params).map((index) => {
                            // todo what if no params?
                            // todo simplify the code, too many same actions in there
                            // todo and also load params from the chunk structure, not from current page, and associate it with current params
                            // it works for now, but what if i add select or something?
                            let type = typeof this.state.params[index];
                            if (type === 'number' || type === 'string') {
                                return <div className={'p-3'}>
                                    <input onInput={(e) => this.onChange(e, index, this.props.index, 'value')} type={type === 'string' ? 'text' : 'number'} name={index} id={`pb_chunk-input-${this.props.index}_${index}`} value={this.state.params[index]} placeholder={this.formatter.ucFirst(index)} title={this.formatter.ucFirst(index)}/>
                                </div>
                            } else if (type === 'boolean') {
                                return <div className={'p-3 d-flex flex-row align-items-center'}>
                                    <input onChange={(e) => this.onChange(e, index, this.props.index, 'checked')} type={'checkbox'} name={index} id={`pb_chunk-input-${this.props.index}_${index}`} checked={this.state.params[index]} title={this.formatter.ucFirst(index)}/>
                                    <label className={'d-block p-2 pt-0 pb-0'} htmlFor={`pb_chunk-input-${this.props.index}_${index}`}>{this.formatter.ucFirst(index)}</label>
                                </div>
                            }
                        })}
                    </div>
                }
            </div>
        </div>
    }
}