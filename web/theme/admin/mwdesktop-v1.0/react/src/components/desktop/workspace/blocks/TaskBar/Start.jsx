import React, {Component} from 'react';

export class Start extends Component {
    onClick(e) {
        this.props.onClick(e)
    }

    render() {
        return <div onClick={(e) => this.onClick(e)} className='Desktop__Workspace__Blocks--TaskBar__Start p-4 pt-2 pb-2 m-0 theme__background-color--hover-soft cursor-pointer' id={'Start'}>
            <div className={'Desktop__Workspace__Blocks--TaskBar__Start-icon w-100 h-100 d-flex flex-row flex-nowrap justify-content-center align-items-center'}>
                <i style={{'fontSize': '30px', 'width': '30px', 'height':'30px'}} className="fab fa-old-republic  theme__link-color d-block"/>
            </div>
        </div>
    }
}