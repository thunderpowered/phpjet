import React from "react";
import {connect} from 'react-redux';
import Taskbar from "./Taskbar";
import {toggleMenu} from "../../actions/menu";

class TaskbarContainer extends React.Component {
    onClickStartButton() { // actually it could be handled by start button itself, i don't know which way is better
        this.props.dispatch(toggleMenu()); // it is a single line, so i assume it doesn't really matter
    }

    onClickTask(event) {

    }

    render() {
        const {windows} = this.props;
        return <Taskbar onClickStartButton={this.onClickStartButton.bind(this)}
                        onClickTask={this.onClickTask.bind(this)}
                        windows={windows}/>
    }
}

const mapStateToProps = state => ({
    windows: state.window.list // .list contains array of all opened windows
});

export default connect(mapStateToProps)(TaskbarContainer)