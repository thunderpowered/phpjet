import React from "react";
import {connect} from "react-redux";
import Menu from "./Menu";
import {PANEL_MODE_CLASSIC} from "../../constants/Mode";
import {logout} from "../../api/auth";

class MenuContainer extends React.Component {

    onClickLogout() {
        this.props.dispatch(logout());
    }

    onClickMenu(id) {
        console.log(id);
    }

    render() {
        if (this.props.opened || this.props.mode === PANEL_MODE_CLASSIC) { // show anyway in classic mode
            return (
                <Menu
                    onClickLogout={this.onClickLogout.bind(this)}
                    onClickMenu={this.onClickMenu}
                />
            )
        } else {
            return null;
        }
    }
}

const mapStateToProps = state => ({
    opened: state.menu.opened,
    mode: state.admin.settings.appearance.mode
});

export default connect(mapStateToProps)(MenuContainer)