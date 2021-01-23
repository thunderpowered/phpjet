import React from "react";
import {connect} from 'react-redux';
import SimpleLoader from './components/loaders/SimpleLoader';
import Workspace from './layouts/Workspace';
import Auth from './layouts/Auth';
import {checkAuthorization} from "./api/auth";

class Desktop extends React.Component {
    constructor(props) {
        super(props);
    }

    componentDidMount() {
        this.setTimeout(() => this.props.dispatch(checkAuthorization), 60000);
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