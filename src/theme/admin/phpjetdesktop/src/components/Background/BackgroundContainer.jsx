import React from 'react';
import {connect} from 'react-redux';
import Background from "./Background";
import {fetchWallpaper} from "../../api/background";
import {openContextMenu} from "../../actions/contextMenu";
import {withTranslation} from "react-i18next";

class BackgroundContainer extends React.Component {
    componentDidMount() {
        this.props.dispatch(fetchWallpaper())
    }
    onChangeWallpaper(event) {
        console.log(event);
    }
    onContextMenu(event) {
        event.preventDefault();
        const {dispatch, t} = this.props;
        dispatch(openContextMenu([
            <a key={0} href={'#'} onClick={this.onChangeWallpaper.bind(this)}>{`${t('Theme.ChangeWallpaper')}...`}</a>
        ], {x: event.clientX, y: event.clientY}));
    }
    render() {
        const {wallpaper} = this.props;
        return (
            <Background onContextMenu={this.onContextMenu.bind(this)} wallpaper={wallpaper}/>
        )
    }
}

const mapStateToProps = state => ({
    wallpaper: state.background.wallpaper
});

export default withTranslation('common')(connect(mapStateToProps)(BackgroundContainer)) // oh dear lord...