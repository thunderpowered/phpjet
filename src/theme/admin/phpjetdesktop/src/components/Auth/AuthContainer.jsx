import React from "react";
import Auth from "./Auth";
import {connect} from 'react-redux';
import {authorizationFirstFactor, authorizationSecondFactor} from "../../api/auth";

class AuthContainer extends React.Component {
    constructor(props) {
        super(props);
        this.actions = {
            '1F': authorizationFirstFactor,
            '2F': authorizationSecondFactor
        }
    }
    render() {
        const action = this.props.action ? this.props.action : '1F'; // ?? '1F'
        return <Auth action={action} onSubmit={this.actions[action]}/>
    }
}

const mapStateToProps = state => ({
    action: state.auth.action
});

export default connect(mapStateToProps)(AuthContainer)