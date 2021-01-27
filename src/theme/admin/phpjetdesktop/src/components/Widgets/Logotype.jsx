import React from "react";
import {connect} from 'react-redux';
import './Logotype.scss';

const Logotype = ({logotype}) => {
    return <div style={{backgroundImage: `url(${logotype})`}} className="Logotype p-3 text-center"/>
};

const mapStateToProps = state => {
    console.log(state);
    return {
    logotype: state.misc.logotype
}};

export default connect(mapStateToProps)(Logotype)