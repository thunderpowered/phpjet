import React, {Component} from 'react';

export class MenuItem extends Component {
    constructor() {
        super();
    }

    render() {
        return (
            <div onContextMenu={this.props.onContextMenu} onClick={this.props.onClick}
                 className={`Desktop__Workspace__Blocks--StartMenu__Item p-4 pt-3 pb-3 theme__border-top theme__border-color theme__background-color--hover-soft theme__border-color--accent fs-5 fw-light d-flex align-items-center ${this.props.active && this.props.panelMode !== 'window' ? 'Desktop__Workspace__Blocks--StartMenu__Item--active theme__background-color2' : ''}`}>
                <div className={'fa-container m-1'}>
                    <i className={`d-inline-block fs-5 fas ${this.props.icon}`}/>
                </div>
                <span className={'d-inline-block fs-6'}>{this.props.label}</span>
            </div>
        );
    }
}