import './scss/index.scss';
import './Desktop.scss';
import React from "react";
import {connect} from 'react-redux';
import Workspace from './layouts/Workspace';
import Auth from './layouts/Auth';
import {checkAuthorization} from "./api/auth";
import {fetchMisc} from "./api/misc";
import SimpleLoader from "./components/Loaders/SimpleLoader";

class Desktop extends React.Component {
    constructor(props) {
        super(props);
    }

    componentDidMount() {
        const {dispatch} = this.props;
        dispatch(checkAuthorization());
        this.interval = setInterval(() => dispatch(checkAuthorization()), 60000);
        dispatch(fetchMisc());
    }

    render() {
        const {authorized} = this.props;
        if (typeof authorized === 'undefined') {
            return <SimpleLoader/>
        }
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