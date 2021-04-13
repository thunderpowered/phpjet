import React from 'react';
import {connect} from 'react-redux';
import Background from "./Background";
import {fetchWallpaper, changeWallpaper} from "../../api/background";
import {closeContextMenu, openContextMenu} from "../../actions/contextMenu";
import {withTranslation} from "react-i18next";

class BackgroundContainer extends React.Component {
    componentDidMount() {
        this.props.dispatch(fetchWallpaper(this.props.admin_id))
    }

    onChangeWallpaper(event) {
        const {dispatch} = this.props;
        if (typeof event.target.files === 'undefined' || typeof event.target.files[0] === 'undefined') return false;
        dispatch(changeWallpaper({file: event.target.files[0]}));
        dispatch(closeContextMenu());
    }

    onContextMenu(event) {
        event.preventDefault();
        const {dispatch, t} = this.props;
        dispatch(openContextMenu([
            <a className={'no-padding'} key={0} href={'#'} onClick={this.onChangeWallpaper.bind(this)}>
                <label htmlFor={'ChangeWallpaper'}
                       className={'w-100 h-100 d-block p-4 pt-2 pb-2'}>{`${t('Theme.ChangeWallpaper')}...`}</label>
                <input id={'ChangeWallpaper'} className={'d-none'} type={'file'}
                       onChange={this.onChangeWallpaper.bind(this)}/>
            </a>
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
    wallpaper: state.background.wallpaper,
    admin_id: state.auth.admin_id
});

export default withTranslation('common')(connect(mapStateToProps)(BackgroundContainer))