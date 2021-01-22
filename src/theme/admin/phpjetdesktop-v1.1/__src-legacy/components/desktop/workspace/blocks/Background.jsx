import React, {Component} from 'react';
import {fetch2} from "../../../../helpers/fetch2";
import {SimpleDropMenu} from "../../elements/dropdowns/SimpleDropMenu";
import {sendFile} from "../../../../helpers/sendFile";

export class Background extends Component {
    constructor() {
        super();
        this.state = {backgroundImage: '', mousePosition: {top: 0, left: 0}, contextMenu: false};
        this.urlGetWallpaper = globalSystemRootURL + globalSystemActions['getWallpaper'];
        this.urlSetWallpaper = globalSystemRootURL + globalSystemActions['setWallpaper'];
        this.loadWallpaper();
    }

    componentDidMount() {
        document.addEventListener('mousedown', () => (
            this.setState(() => ({contextMenu: false}))
        ));
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

    changeWallpaper(event) {
        return sendFile(this.urlSetWallpaper, {
            queryParams: {
                file: event.target.files[0]
            }
        }, {
            onSuccess: (result) => {
                this.setState(() => ({contextMenu: false}));
                if (typeof result.data !== 'undefined' && typeof result.data.wallpaper !== 'undefined') {
                    this.setWallpaper(result.data.wallpaper);
                } else {
                    // try to load it again, maybe it's just an error or something
                    this.loadWallpaper();
                }
            }
        })
    }

    setWallpaper(wallpaper) {
        this.setState(() => ({backgroundImage: wallpaper}));
    }

    onContextMenu(e) {
        // disable browser's context menu
        e.preventDefault();
        e.stopPropagation();
        this.setState(() => ({
            mousePosition: {
                top: e.clientY,
                left: e.clientX
            },
            contextMenu: true
        }));
    }

    render() {
        return <div onContextMenu={(e) => this.onContextMenu(e)}
                    style={{'backgroundImage': `url('${this.state.backgroundImage}')`}}
                    className={'Desktop__Workspace__Blocks--Background vh-100 w-100 position-absolute overflow-hidden theme__background-color theme__background-image theme__background-image--cover'}
                    id={'Background'}>

            <SimpleDropMenu active={this.state.contextMenu} mouse={this.state.mousePosition} hoverClass={'theme__background-color--hover'}>
                {/* Background context menu */}
                <div onMouseDown={(e) => e.stopPropagation()}>
                    <label
                        className={'w-100 h-100 p-4 pt-2 pb-2 d-block Desktop__Workspace__Blocks--Background__ContextMenu--FileUpload theme__cursor-pointer'}
                        htmlFor={'BackgroundChangeInput'}>Change wallpaper</label>
                    <input id={'BackgroundChangeInput'} className={'d-none'} type={'file'}
                           onChange={this.changeWallpaper.bind(this)}/>
                </div>
            </SimpleDropMenu>
        </div>
    }
}