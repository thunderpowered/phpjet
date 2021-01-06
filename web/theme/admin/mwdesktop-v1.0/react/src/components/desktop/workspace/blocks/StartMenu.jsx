import React, {Component} from 'react';
import {Logotype} from "../../elements/widgets/Logotype";
import {EngineVersion} from "../../elements/widgets/EngineVersion";
import {MenuItem} from "./StartMenu/MenuItem";
import {SimpleDropMenu} from "../../elements/dropdowns/SimpleDropMenu";

export class StartMenu extends Component {

    constructor(props) {
        super(props);
        this.ref = React.createRef();
        this.showStartMenu = false;
        this.state = {mousePosition: {top: 0, left: 0}, contextMenu: false, index: 0};
    }

    componentDidMount() {
        document.addEventListener('mousedown', () => {
            this.setState(() => ({contextMenu: false}));
        });
    }

    onContextMenu(e, index) {
        e.preventDefault();
        e.stopPropagation();
        this.setState(() => ({
            mousePosition: {top: e.clientY, left: e.clientX},
            contextMenu: true,
            'index': index
        }));
    }

    onContextMenuClick(e, index) {
        this.props.onContextMenu(e, index);
        this.setState(() => ({contextMenu: false}));
    }

    render() {
        this.showStartMenu = this.props.showStartMenu;
        let display = this.showStartMenu ? 'block' : 'none';
        return <div
            className={`Desktop__Workspace__Blocks--StartMenu-wrapper vh-100 position-absolute overflow-hidden theme__background-transparent Desktop__Workspace__Blocks--StartMenu-wrapper--${display}`}>
            <div style={{display: display}} className="container h-100">
                <div className="row h-100">
                    {/* i'm not sure do i still need a bootstrap grid here */}
                    <div className="col-lg-12 col-md-12 col-sm-12 h-100 p-0 d-flex flex-column justify-content-end">
                        <div ref={this.ref}
                             className={'mb-5 pb-2 theme__background-color3 d-flex flex-column justify-content-end'}
                             id={'StartMenu'} onClick={(e) => e.stopPropagation()}>

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
                                    <MenuItem active={this.props.windowOnTop === index} panelMode={this.props.panelMode} onClick={() => this.props.onClickMenu(index)} icon={item.icon}
                                              label={item.label} index={index} onContextMenu={(e) => this.onContextMenu(e, index)}/>
                                ))
                                }

                                {/* Proceed logout manually */}
                                <MenuItem onClick={this.props.onClickLogout} icon={'fa-sign-out-alt'}
                                          label={'Sign out'}/>
                            </div>
                        </div>
                    </div>
                </div>

                <SimpleDropMenu active={this.state.contextMenu} mouse={this.state.mousePosition} hoverClass={'theme__background-color--hover'}>
                    <div onMouseDown={(e) => e.stopPropagation()}>
                        <div className={'w-100 h-100 p-4 pt-2 pb-2 d-block theme__cursor-pointer'} onClick={(e) => {this.onContextMenuClick(e, this.state.index)}}>
                            Set this window as Default
                        </div>
                    </div>
                    <div onMouseDown={(e) => e.stopPropagation()}>
                        <div className={'w-100 h-100 p-4 pt-2 pb-2 d-block theme__cursor-pointer'} onClick={(e) => {console.log('todo')}}>
                            Refresh window (//todo)
                        </div>
                    </div>
                </SimpleDropMenu>

            </div>
        </div>

    }
}