import React, {Component} from 'react';
import {Logotype} from "../../elements/widgets/Logotype";
import {EngineVersion} from "../../elements/widgets/EngineVersion";
import {MenuItem} from "./StartMenu/MenuItem";

export class StartMenu extends Component {

    constructor(props) {
        super(props);
        this.ref = React.createRef();
        this.showStartMenu = false;
    }

    componentDidMount() {
        document.addEventListener('mousedown', this.closeMenu.bind(this));
    }

    closeMenu(event) {
        // register click outside of block to close it
        // todo handle if click on
        if (this.ref && !this.ref.current.contains(event.target)) {
            if (this.showStartMenu) {
                this.props.onClickStart(false);
            }
        }
    }

    render() {
        this.showStartMenu = this.props.showStartMenu;
        return <div
            className={'Desktop__Workspace__Blocks--StartMenu-wrapper vh-100 position-absolute overflow-hidden theme__background-transparent'}>
            <div style={{display: (this.showStartMenu ? 'block' : 'none')}} className="container h-100">
                <div className="row h-100">
                    {/* i'm not sure do i still need a bootstrap grid here */}
                    <div className="col-lg-12 col-md-12 col-sm-12 h-100 p-0 d-flex flex-column justify-content-end">
                        <div ref={this.ref}
                             className={'mb-5 pb-2 theme__background-color3 d-flex flex-column justify-content-end'}
                             id={'StartMenu'}>

                            {/* Menu header */}
                            <div className="Desktop__Workspace__Blocks--StartMenu__Widget p-4">
                                <Logotype/>
                                <div className={'pt-3 pb-0'}>
                                    <EngineVersion/>
                                </div>
                            </div>

                            {/* Actual menu list */}
                            <div className="Desktop__Workspace__Blocks--StartMenu__Rack js-plugin_niceScroll">
                                {this.props.windowConfig.map((item, index) => (
                                    <MenuItem onClick={() => this.props.onClickMenu(index)} icon={item.icon} label={item.label}/>
                                ))
                                }

                                {/* Proceed logout manually */}
                                <MenuItem onClick={this.props.onClickLogout} icon={'fa-sign-out-alt'} label={'Sign out'}/>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>

    }
}