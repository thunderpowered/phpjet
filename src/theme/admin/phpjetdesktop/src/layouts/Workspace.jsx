import React from 'react';
import {connect} from 'react-redux';
import {PANEL_MODE_WINDOW} from "../constants/mode";
import {BackgroundContainer} from "../components/Background/BackgroundContainer";

class Workspace extends React.Component {
    render() {
        return (
            <div className="Workspace">
                {this.props.panelMode === PANEL_MODE_WINDOW &&
                    <BackgroundContainer/>
                }
                Workspace...
            </div>
        )
    }
}

const mapStateToProps = state => ({
    mode: state.mode
});

export default connect(mapStateToProps)(Workspace)