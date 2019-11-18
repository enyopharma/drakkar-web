import React, { useState } from 'react'
import Modal from 'react-bootstrap4-modal';

const ActionsFieldset = ({ top, feedback, saving, savable, resetable, save, reset }) => {
    const [modal, setModal] = useState(false)

    const submitReset = () => {
        reset()
        setModal(false)
        document.getElementById(top).scrollIntoView()
    }

    return (
        <fieldset>
            <legend>
                <span className={'fas fa-circle small text-primary'}></span>
                &nbsp;
                Actions
            </legend>
            <div className="row">
                <div className="col-6">
                    <button
                        type="button"
                        className="btn btn-block btn-primary"
                        onClick={e => save()}
                        disabled={saving || !savable}
                    >
                        {saving
                            ? <span className="spinner-border spinner-border-sm"></span>
                            : <span className="fas fa-save" />
                        }
                        &nbsp;
                        Save description
                    </button>
                </div>
                <div className="col-6">
                    <button
                        type="button"
                        className="btn btn-block btn-primary"
                        onClick={e => setModal(true)}
                        disabled={!resetable}
                    >
                        <span className="fas fa-eraser"></span> Reset form data
                    </button>
                </div>
            </div>
            <Modal visible={modal} dialogClassName="modal-lg">
                <div className="modal-header">
                    <h5 className="modal-title">
                        Reseting form data
                    </h5>
                    <button type="button" className="close" onClick={e => setModal(false)}>
                        &times;
                    </button>
                </div>
                <div className="modal-body">
                    Are you sure you want to reset the form data?
                </div>
                <div className="modal-footer">
                    <button
                        type="button"
                        className="btn btn-block btn-primary"
                        onClick={e => submitReset()}
                    >
                        <span className="fas fa-eraser"></span> Reset form data
                    </button>
                </div>
            </Modal>
            {feedback == null ? null : (
                <div className={feedback.success ? 'text-success' : 'text-danger'}>
                    {feedback.success
                        ? 'Description successfully saved!'
                        : <ul>{feedback.errors.map((e, i) => <li key={i}>{e}</li>)}</ul>
                    }
                </div>
            )}
        </fieldset>
    )
}

export default ActionsFieldset
