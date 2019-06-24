import React, { useState } from 'react'
import Modal from 'react-bootstrap4-modal';

import api from '../api'
import MappingModal from './MappingModal'

const Table = ({ descriptions }) => {
    const [selected, setSelected] = useState(null)
    const [deleting, setDeleting] = useState(null)
    const [deleted, setDeleted] = useState(descriptions.map(d => d.deleted))

    const editUrl = (index) => {
        const { run_id, pmid, id } = descriptions[index]

        return `/runs/${run_id}/publications/${pmid}/descriptions/${id}/edit`
    }

    const openCopy = (index) => {
        window.open(editUrl(index))
    }

    const showMapping = (index) => {
        setSelected(descriptions[index])
    }

    const hideMapping = () => {
        setSelected(null)
    }

    const deleteDescription = (index) => {
        setDeleting(index)
    }

    const confirmDeletion = () => {
        const { run_id, pmid, id } = descriptions[deleting]

        api.descriptions.delete(run_id, pmid, id).then(json => {
            setDeleted(deleted.map((d, i) => d || i == deleting))
            setDeleting(null)
        })
    }

    const cancelDeletion = () => {
        setDeleting(null)
    }

    return (
        <React.Fragment>
            <table className="table">
                <thead>
                    <tr>
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
                            <td className="text-center">
                                <span title={description.method.name}>
                                    {description.method.psimi_id}
                                </span>
                            </td>
                            <td className="text-center">
                                {[
                                    description.interactor1.protein.accession, '/',
                                    description.interactor1.name,
                                ].join('')}
                            </td>
                            <td className="text-center">
                                {description.type == 'hh'
                                    ? [
                                        description.interactor2.protein.accession, '/',
                                        description.interactor2.name,
                                    ].join('')
                                    : [
                                        description.interactor2.protein.accession, '/',
                                        description.interactor2.name, '(',
                                        description.interactor2.start, ', ',
                                        description.interactor2.stop, ')',
                                    ].join('')}
                            </td>
                            <td className="text-center">
                                {description.interactor1.mapping.length == 0
                                    && description.interactor2.mapping.length == 0 ? (
                                    <span className="text-muted">-</span>
                                ) : (
                                    <a href="#" onClick={(e) => { e.preventDefault(); showMapping(i) }}>
                                        <i className="fas fa-search"></i> Mapping
                                    </a>
                                )}
                            </td>
                            <td className="text-center">
                                {description.created_at}
                            </td>
                            <td className="text-center">
                                {description.deleted_at}
                            </td>
                            <td className="text-center">
                                <a href={editUrl(i)} onClick={(e) => { e.preventDefault(); openCopy(i) }}>
                                    <i className="fas fa-copy"></i> Copy
                                </a>
                            </td>
                            <td className="text-center">
                                {deleted[i] ? (
                                    <span className="text-muted">
                                        <i className="fas fa-trash"></i> Delete
                                    </span>
                                ) : (
                                    <a href="#" className="text-danger" onClick={(e) => { e.preventDefault(); deleteDescription(i) }}>
                                        <i className="fas fa-trash"></i> Delete
                                    </a>
                                )}
                            </td>
                        </tr>
                    ))}
                </tbody>
            </table>
            {selected == null ? null : (
                <MappingModal description={selected} close={hideMapping} />
            )}
            {deleting == null ? null : (
                <Modal visible={true} dialogClassName="modal-lg">
                    <div className="modal-header">
                        <h5 className="modal-title">
                            Deleting description from publication {descriptions[deleting].pmid}
                        </h5>
                        <button type="button" className="close" onClick={e => cancelDeletion()}>
                            &times;
                        </button>
                    </div>
                    <div className="modal-body">
                        Are you sure you want to delete a description?
                    </div>
                    <div className="modal-footer">
                        <button type="button" className="btn btn-block btn-danger" onClick={e => confirmDeletion()}>
                            Delete description
                        </button>
                    </div>
                </Modal>
            )}
        </React.Fragment>
    )
}

export default Table
