import './scss/index.scss';
import './Desktop.scss';
import React from "react";
import {connect} from 'react-redux';
import Workspace from './layouts/Workspace';
import Auth from './layouts/Auth';
import {checkAuthorization} from "./api/auth";

class Desktop extends React.Component {
    constructor(props) {
        super(props);
    }

    componentDidMount() {
        this.props.dispatch(checkAuthorization());
        this.interval = setInterval(() => this.props.dispatch(checkAuthorization()), 60000);
    }

    render() {
        if (this.props.authorized) {
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