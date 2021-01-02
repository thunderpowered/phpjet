import React, {Component} from 'react';
import {Authenticator} from "../../classes/Authenticator";
import {Workspace} from "./workspace/Workspace.";
import {Auth} from "./workspace/Auth";

export class Desktop extends Component {
    constructor(props) {
        super(props);
        this.authenticator = new Authenticator();
        this.checkAuthorization();
        this.state = {};
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
            return <div>Loading...</div>
        }

        if (this.state.authorized) {
            return <Workspace/>
        } else {
            return <Auth callback={this.proceedAuthorization.bind(this)}/>
        }
    }
}