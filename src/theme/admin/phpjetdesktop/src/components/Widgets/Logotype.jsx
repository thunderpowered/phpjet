import React from "react";
import {connect} from 'react-redux';
import './Logotype.scss';

const Logotype = ({logotype}) => {
    return <div style={{backgroundImage: `url(${logotype})`}} className="logotype p-3 text-center"/>
};

const mapStateToProps = state => ({
    logotype: state.misc.logotype
});

export default connect(mapStateToProps)(Logotype)