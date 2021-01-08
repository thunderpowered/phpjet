import React, {Component} from 'react';
import {Authenticator} from "../../classes/Authenticator";
import {Workspace} from "./workspace/Workspace.";
import {Auth} from "./workspace/Auth";
import {SimpleLoader} from "./elements/loaders/SimpleLoader";

export class Desktop extends Component {
    constructor(props) {
        super(props);
        this.state = {};
        this.authenticator = new Authenticator();
        this.checkAuthorization();
    }

    logout() {
        return this.authenticator.logout(this.proceedAuthorization.bind(this));
    }

    checkAuthorization() {
        return this.authenticator.isAdminAuthorized(this.proceedAuthorization.bind(this), true);
    }

    proceedAuthorization(authorized) {
        if (this.state.authorized !== authorized) {
            this.setState(() => ({'authorized': authorized}));
        }
    }

    render() {
        if (typeof this.state.authorized === 'undefined') {
            return <SimpleLoader/>
        }

        if (this.state.authorized) {
            return <Workspace onClickLogout={this.logout.bind(this)}/>
        } else {
            return <Auth callback={this.proceedAuthorization.bind(this)}/>
        }
    }
}