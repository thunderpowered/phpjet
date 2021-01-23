import React from "react";
import {connect} from 'react-redux';
import SimpleLoader from './components/loaders/SimpleLoader';
import Workspace from './layouts/Workspace';
import Auth from './layouts/Auth';
import {Authenticator} from "./tools/Authenticator";
import {checkAuthorization} from "./actions/auth";

class Desktop extends React.Component {
    constructor(props) {
        super(props);
        this.authenticator = new Authenticator();
    }

    componentDidMount() {
        const {dispatch} = this.props;
        this.authenticator.checkAuthentication((authorized, urls) => (
            dispatch(checkAuthorization(authorized, urls))
        ));
    }

    render() {
        const {authorized} = this.props;
        if (typeof authorized === 'undefined') {
            return <SimpleLoader/>
        }
        if (authorized) {
            return <Workspace/>
        } else {
            return <Auth/>
        }
    }
}

const mapStateToProps = state => ({
    authorized: state.auth.authorized
});


export default connect(mapStateToProps)(Desktop)