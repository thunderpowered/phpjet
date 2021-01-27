import React from 'react';
import {connect} from 'react-redux';
import {PANEL_MODE_WINDOW} from "../constants/mode";
import BackgroundContainer from "../components/Background/BackgroundContainer";
import ContextMenuContainer from "../components/ContextMenu/ContextMenuContainer";
import {closeContextMenu} from "../actions/contextMenu";

class Workspace extends React.Component {
    onClickMaster = () => {
        this.props.dispatch(closeContextMenu());
    };
    render() {
        const {mode} = this.props;
        return (
            <div className="Workspace" onClick={this.onClickMaster.bind(this)}>
                {mode === PANEL_MODE_WINDOW &&
                    <BackgroundContainer/>
                }
                <BackgroundContainer/> {/* temp */}
                Workspace...

                <ContextMenuContainer/>
            </div>
        )
    }
}

const mapStateToProps = state => ({
    mode: state.mode
});

export default connect(mapStateToProps)(Workspace)