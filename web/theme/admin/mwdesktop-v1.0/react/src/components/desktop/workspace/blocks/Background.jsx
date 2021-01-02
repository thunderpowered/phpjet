import React, {Component} from 'react';

export class Background extends Component {
    state = {wallpaper: ''};

    constructor() {
        super();
    }

    loadWallpaper() {
        console.log('loading wallpaper...');
    }

    onClick(event) {
        console.log(event);
    }

    render() {
        return <div onClick={(e) => this.onClick(e)} style={{'backgroundImage': `url('${this.state.wallpaper}')`}}
                    className={'Desktop__Workspace__Blocks--Background vh-100 w-100 position-absolute overflow-hidden theme__background-color theme__background-image theme__background-image--cover'}
                    id={'Background'}/>
    }
}