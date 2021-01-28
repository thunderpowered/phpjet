export const SET_MENU_VISIBILITY = 'SET_MENU_VISIBILITY';
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