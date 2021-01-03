import React, {Component} from 'react';
import {Logotype} from "../../../elements/widgets/Logotype";

export class StartMenu extends Component {

    onClickLogout(e) {
        this.props.onClickLogout();
    }
    // i changed a bit, so i'm pretty sure i don't need boostrap grid here anymore
    // todo
    render() {
        let {showStartMenu} = this.props;
        return <div style={{display: (showStartMenu ? 'block' : 'none')}} className="container h-100">
            <div className="row h-100">
                <div className="col-lg-12 col-md-12 col-sm-12 h-100 p-0 d-flex flex-column justify-content-end">
                    <div className={'mb-5 pb-2 theme__background-color3 d-flex flex-column justify-content-end'}
                         id={'StartMenu'}>
                        <div className="Desktop__Workspace__WindowSpace--StartMenu__Widget p-4">
                            <Logotype/>
                        </div>
                        <div className="Desktop__Workspace__WindowSpace--StartMenu__Rack js-plugin_niceScroll">
                            {/* Proceed logout manually */}
                            <div onClick={(e) => this.onClickLogout(e)} className={'Desktop__Workspace__WindowSpace--StartMenu__Item p-4 pt-3 pb-3 theme__border-top theme__border-color theme__background-color--hover-soft fs-5 fw-light'}>
                                <i className="fas fa-sign-out-alt" /> Sign Out
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    }
}