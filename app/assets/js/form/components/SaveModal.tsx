import React from 'react'
import Modal from 'react-bootstrap4-modal';
import { FaSave } from 'react-icons/fa'

type Props = {
    stable_id: string
    show: boolean
    save: () => void
    close: () => void
}

export const SaveModal: React.FC<Props> = ({ stable_id, show, save, close }) => (
    <Modal visible={show} dialogClassName="modal-lg">
        <div className="modal-header">
            <h5 className="modal-title">
                Saving form data
            </h5>
            <button type="button" className="close" onClick={e => close()}>
                &times;
            </button>
        </div>
        <div className="modal-body">
            Are you sure you want to create a new version of description {stable_id}?
        </div>
        <div className="modal-footer">
            <button
                type="button"
                className="btn btn-block btn-primary"
                onClick={e => save()}
            >
                <FaSave /> Save form data
            </button>
        </div>
    </Modal>
)
