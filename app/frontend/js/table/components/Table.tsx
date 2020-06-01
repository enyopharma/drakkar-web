import React, { useState } from 'react'
import Modal from 'react-bootstrap4-modal';
import { FaSearch, FaTrash, FaCopy } from 'react-icons/fa'

import { descriptions as api } from '../api'
import { Description, Interactor } from '../types'

import { MappingModal } from './MappingModal'

type Props = {
    descriptions: Description[]
}

export const Table: React.FC<Props> = ({ descriptions }) => {
    const [selected, setSelected] = useState<Description | null>(null)
    const [deleting, setDeleting] = useState<number | null>(null)
    const [deleted, setDeleted] = useState<boolean[]>(descriptions.map(d => d.deleted))

    const showMapping = (index: number) => {
        setSelected(descriptions[index])
    }

    const hideMapping = () => {
        setSelected(null)
    }

    const deleteDescription = (index: number) => {
        setDeleting(index)
    }

    const confirmDeletion = () => {
        if (!deleting) return

        const { id, pmid, run_id } = descriptions[deleting]

        api.delete(run_id, pmid, id).then(_ => {
            setDeleted(deleted.map((d, i) => d || i == deleting))
            setDeleting(null)
        })
    }

    const cancelDeletion = () => {
        setDeleting(null)
    }

    return (
        <React.Fragment>
            {selected && <MappingModal description={selected} close={hideMapping} />}
            {deleting && <DeleteModal pmid={descriptions[deleting].pmid} cancel={cancelDeletion} confirm={confirmDeletion} />}
            <table className="table">
                <thead>
                    <tr>
                        <th className="text-center">Stable id</th>
                        <th className="text-center">Method</th>
                        <th className="text-center">Interactor 1</th>
                        <th className="text-center">Interactor 2</th>
                        <th className="text-center">Mapping</th>
                        <th className="text-center">Created at</th>
                        <th className="text-center">Deleted at</th>
                        <th className="text-center">Copy</th>
                        <th className="text-center">Delete</th>
                    </tr>
                </thead>
                <tbody>
                    {descriptions.map((description, i) => (
                        <tr key={i} className={deleted[i] ? 'table-danger' : ''}>
                            <td className="text-center align-middle">
                                {description.stable_id}
                            </td>
                            <td className="text-center align-middle">
                                {description.method.psimi_id}
                            </td>
                            <td className="text-center align-middle">
                                {InteractorName(description.interactor1)}
                            </td>
                            <td className="text-center align-middle">
                                {InteractorName(description.interactor2)}
                            </td>
                            <td className="text-center align-middle">
                                <MappingLink {...description} show={() => showMapping(i)} />
                            </td>
                            <td className="text-center align-middle">
                                {description.created_at}
                            </td>
                            <td className="text-center align-middle">
                                {description.deleted_at}
                            </td>
                            <td className="text-center align-middle">
                                <CopyLink {...description} />
                            </td>
                            <td className="text-center align-middle">
                                <DeleteButton deleted={deleted[i]} update={() => deleteDescription(i)} />
                            </td>
                        </tr>
                    ))}
                </tbody>
            </table>
        </React.Fragment>
    )
}

const InteractorName = (interactor: Interactor) => (
    `${interactor.protein.accession}/${interactor.name}`
)

const CopyLink: React.FC<{ id: number, pmid: number, run_id: number }> = ({ id, pmid, run_id }) => {
    const url = `/runs/${run_id}/publications/${pmid}/descriptions/${id}/edit`
    const classes = "btn btn-block btn-sm btn-outline-primary"

    return (
        <a className={classes} href={url}>
            <FaCopy /> Copy
        </a>
    )
}

const MappingLink: React.FC<{ interactor1: Interactor, interactor2: Interactor, show: () => void }> = props => {
    const classes = "btn btn-block btn-sm btn-outline-primary"

    const { interactor1, interactor2, show } = props

    if (interactor1.mapping.length + interactor2.mapping.length === 0) {
        return <React.Fragment>-</React.Fragment>
    }

    return (
        <button className={classes} onClick={e => show()}>
            <FaSearch /> Mapping
        </button>
    )
}

const DeleteButton: React.FC<{ deleted: boolean, update: () => void }> = ({ deleted, update }) => {
    const classes = "btn btn-block btn-sm btn-outline-danger"

    return (
        <button className={classes} disabled={deleted} onClick={e => update()}>
            <FaTrash /> Delete
        </button>
    )
}

const DeleteModal: React.FC<{ pmid: number, cancel: () => void, confirm: () => void }> = ({ pmid, cancel, confirm }) => (
    <Modal visible={true} dialogClassName="modal-lg">
        <div className="modal-header">
            <h5 className="modal-title">
                Deleting description from publication {pmid}
            </h5>
            <button type="button" className="close" onClick={e => cancel()}>
                &times;
            </button>
        </div>
        <div className="modal-body">
            Are you sure you want to delete this description?
                    </div>
        <div className="modal-footer">
            <button type="button" className="btn btn-block btn-danger" onClick={e => confirm()}>
                Delete description
            </button>
        </div>
    </Modal>
)
