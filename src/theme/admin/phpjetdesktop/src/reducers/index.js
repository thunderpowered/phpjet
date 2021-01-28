import {combineReducers} from "redux";
import auth from "./auth";
import background from "./background";
import forms from "./forms";
import misc from "./misc";
import contextMenu from "./contextMenu";
import workspace from "./workspace";
import window from "./window";
import menu from "./menu";

export default combineReducers({
    auth,
    background,
    forms,
    misc,
    contextMenu,
    workspace,
    window,
    menu
})