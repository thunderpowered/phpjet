import api from "../tools/api";
import {setAdminSettings} from "../actions/admin";

export const loadAdminSettings = (admin_id, settings) => (
    dispatch => (
        api.get(`admin/${admin_id}/settings/${settings}`, {}, result => (
            dispatch(setAdminSettings(settings, result.data.appearance))
        ))
    )
);