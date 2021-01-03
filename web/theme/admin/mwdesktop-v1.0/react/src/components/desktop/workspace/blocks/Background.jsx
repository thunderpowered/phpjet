import React, {Component} from 'react';
import {fetch2} from "../../../../helpers/fetch2";

export class Background extends Component {
    constructor() {
        super();
        this.state = {backgroundImage: ''};
        this.urlGetWallpaper = '/admin/misc/getWallpaper';
        this.loadWallpaper();
    }

    loadWallpaper() {
        return fetch2(this.urlGetWallpaper, {}, {
            onSuccess: (result) => {
                if (typeof result.data !== 'undefined' && typeof result.data.wallpaper !== 'undefined') {
                    this.setWallpaper(result.data.wallpaper);
                }
            }
        });
    }

    setWallpaper(wallpaper) {
        this.setState(() => ({backgroundImage: wallpaper}));
    }

    onContextMenu(e) {
        // disable browser's context menu
        e.preventDefault();
        let xPosition = e.pageX;
        let yPosition = e.pageY;

        console.log(xPosition);
        console.log(yPosition);
    }

    render() {
        return <div onContextMenu={(e) => this.onContextMenu(e)} style={{'backgroundImage': `url('${this.state.backgroundImage}')`}}
                    className={'Desktop__Workspace__Blocks--Background vh-100 w-100 position-absolute overflow-hidden theme__background-color theme__background-image theme__background-image--cover'}
                    id={'Background'}/>
    }
}