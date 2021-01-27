import {combineReducers} from "redux";
import auth from "./auth";
import background from "./background";
import forms from "./forms";
import misc from "./misc";
import contextMenu from "./contextMenu";

export default combineReducers({
    auth,
    background,
    forms,
    misc,
    contextMenu
})