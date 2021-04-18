import React from "react";
import './Version.scss';
import {connect} from "react-redux";

const Version = ({version}) => {
    return <div className="version text-center d-block fs-8 p-3">{version}</div>
};

const mapStateToProps = state => ({
    version: state.misc.version
});

export default connect(mapStateToProps)(Version)