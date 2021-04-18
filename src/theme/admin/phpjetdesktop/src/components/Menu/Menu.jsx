import React, {useRef} from "react";
import './Menu.scss';
import Logotype from "../Widgets/Logotype";
import Version from "../Widgets/Version";
import MenuItem from "./MenuItem";
import {useTranslation} from "react-i18next";

const Menu = ({list, onClickMenu, onClickLogout}) => {
    const menuRef = useRef(null);
    const {t} = useTranslation();

    return (
        <div id={'Menu'} className={'menu vh-100 position-absolute overflow-hidden'}>
            <div className="container h-100">
                <div className="row h-100">
                    <div className="col-lg-12 col-md-12 col-sm-12 h-100 p-0 d-flex flex-column justify-content-end">
                        <div onClick={e => e.stopPropagation()} ref={menuRef} className="menu__inner mb-5 pb-2 d-flex flex-column justify-content-end">
                            {/* header */}
                            <div className="menu__header">
                                <Logotype/>
                                <div className="pt-3 pb-0">
                                    <Version/>
                                </div>
                            </div>
                            {/* menu list */}
                            <div className="menu__list js-plugin_niceScroll">
                                {/* actual menu list */}
                                {list && list.length > 0 &&
                                    list.map(menuItem => (
                                        <div>menu item</div>
                                    ))
                                }
                                {/* custom items */}
                                <MenuItem
                                    onClick={() => onClickLogout()}
                                    icon={'fa-sign-out-alt'}
                                    label={t('Auth.Logout')}
                                />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    )
}

export default Menu