import {combineReducers} from "redux";
import auth from "./auth";
import background from "./background";
import forms from "./forms";
import misc from "./misc";

export default combineReducers({
    auth,
    background,
    forms,
    misc
})