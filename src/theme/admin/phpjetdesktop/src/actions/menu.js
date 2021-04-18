export const SET_MENU_VISIBILITY = 'SET_MENU_VISIBILITY';
export const SET_MENU_LIST = 'SET_MENU_LIST';
export const TOGGLE_MENU = 'TOGGLE_MENU';

export const toggleMenu = () => ({
    type: TOGGLE_MENU
});

export const openMenu = () => ({
    type: SET_MENU_VISIBILITY,
    opened: true
});

export const closeMenu = () => ({
    type: SET_MENU_VISIBILITY,
    opened: false
});

export const setMenuList = list => ({
    type: SET_MENU_LIST,
    list
});