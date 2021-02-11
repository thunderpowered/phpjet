import React from 'react';
import SimplePopup from "../components/Popups/SimplePopup";
import AuthContainer from "../components/Auth/AuthContainer";

class Auth extends React.Component {
    render() {
        return (
            <SimplePopup>
                <AuthContainer/>
            </SimplePopup>
        )
    }
}

export default Auth