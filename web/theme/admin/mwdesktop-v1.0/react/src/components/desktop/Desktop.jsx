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

        window.onerror = function(message, file, line, col, error) {
            Msg.error("Desktop crashed. Error information automatically reported to development team, we will handle this as soon as possible. The Desktop will be automatically reloaded in 5 sec.");

            // todo report frontend errors too
            console.log(message);
            setTimeout(() => {
                window.location.reload();
            });
            return false;
        };
    }

    logout() {
        return this.authenticator.logout(this.proceedAuthorization.bind(this));
    }

    checkAuthorization() {
        return this.authenticator.isAdminAuthorized(this.proceedAuthorization.bind(this), true);
    }

    proceedAuthorization(authorized, urls) {
        if (typeof urls !== 'undefined') {
            globalSystemActions = urls;
        }

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