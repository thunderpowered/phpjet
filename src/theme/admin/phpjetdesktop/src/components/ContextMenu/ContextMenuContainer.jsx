import React from "react";
import {connect} from 'react-redux';
import ContextMenu from "./ContextMenu";

class ContextMenuContainer extends React.Component {
    render() {
        const {children, mousePosition, opened} = this.props;
        return (
            <ContextMenu opened={opened} mousePosition={mousePosition}>
                {children}
            </ContextMenu>
        )
    }
}

const mapStateToProps = state => state.contextMenu;

export default connect(mapStateToProps)(ContextMenuContainer)