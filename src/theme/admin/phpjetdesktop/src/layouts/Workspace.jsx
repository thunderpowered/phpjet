import React from 'react';
import {connect} from 'react-redux';
import {PANEL_MODE_WINDOW} from "../constants/mode";
import BackgroundContainer from "../components/Background/BackgroundContainer";
import {closeContextMenu} from "../actions/contextMenu";
import ContextMenu from "../components/ContextMenu/ContextMenu";
import TaskbarContainer from "../components/Taskbar/TaskbarContainer";
import {loadAdminSettings} from "../api/admin";

class Workspace extends React.Component {
    onClickMaster = () => {
        this.props.dispatch(closeContextMenu());
    };

    componentDidMount() {
        console.log(this.props);
        this.props.dispatch(loadAdminSettings(this.props.admin_id, 'appearance'));
    }

    render() {
        const {mode} = this.props;
        return (
            <div className="Workspace" onClick={this.onClickMaster.bind(this)}>
                {mode === PANEL_MODE_WINDOW &&
                    <BackgroundContainer/>
                }
                <TaskbarContainer/>
                <ContextMenu/>
            </div>
        )
    }
}

const mapStateToProps = state => ({
    mode: state.workspace.mode,
    admin_id: state.auth.admin_id
});

export default connect(mapStateToProps)(Workspace)