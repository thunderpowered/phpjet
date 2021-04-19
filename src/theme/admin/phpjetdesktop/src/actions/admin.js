export const SET_ADMIN_SETTINGS = 'SET_ADMIN_SETTINGS';

export const setAdminSettings = (settings, data) => ({
    type: SET_ADMIN_SETTINGS,
    settings,
    data
});