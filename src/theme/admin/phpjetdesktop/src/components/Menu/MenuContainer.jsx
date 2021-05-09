import React from "react";
import {connect} from "react-redux";
import Menu from "./Menu";
import {PANEL_MODE_CLASSIC} from "../../constants/Mode";
import {logout} from "../../api/auth";
import {fetchMenu} from "../../api/menu";

class MenuContainer extends React.Component {

    componentDidMount() {
        this.props.dispatch(fetchMenu());
    }

    onClickLogout() {
        this.props.dispatch(logout());
    }

    onClickMenu(id) {
        console.log(id);
    }

    render() {
        const {list, mode, opened} = this.props;
        if (opened || mode === PANEL_MODE_CLASSIC) { // show anyway in classic mode
            return (
                <Menu
                    list={list}
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
    list: state.menu.list,
    opened: state.menu.opened,
    mode: state.admin.settings.appearance.mode
});

export default connect(mapStateToProps)(MenuContainer)