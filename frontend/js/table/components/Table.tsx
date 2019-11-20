import React, { useState } from 'react'
import Modal from 'react-bootstrap4-modal';

import { descriptions as api } from '../api'
import { Description } from '../types'

import { MappingModal } from './MappingModal'

type Props = {
    descriptions: Description[],
}

const StatelessTable: React.FC<Props> = ({ descriptions }) => {
    const [selected, setSelected] = useState<Description>(null)
    const [deleting, setDeleting] = useState<number>(null)
    const [deleted, setDeleted] = useState<boolean[]>(descriptions.map(d => d.deleted))

    const editUrl = (index: number): string => {
        const id = descriptions[index].id
        const pmid = descriptions[index].pmid
        const run_id = descriptions[index].run_id

        return `/runs/${run_id}/publications/${pmid}/descriptions/${id}/edit`
    }

    const showMapping = (index: number): void => {
        setSelected(descriptions[index])
    }

    const hideMapping = (): void => {
        setSelected(null)
    }

    const deleteDescription = (index: number): void => {
        setDeleting(index)
    }

    const confirmDeletion = (): void => {
        const id = descriptions[deleting].id
        const pmid = descriptions[deleting].pmid
        const run_id = descriptions[deleting].run_id

        api.delete(run_id, pmid, id).then(_ => {
            setDeleted(deleted.map((d, i) => d || i == deleting))
            setDeleting(null)
        })
    }

    const cancelDeletion = (): void => {
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
                                {description.method.psimi_id}
                            </td>
                            <td className="text-center">
                                {[
                                    description.interactor1.protein.accession, '/',
                                    description.interactor1.name,
                                ].join('')}
                            </td>
                            <td className="text-center">
                                {[
                                    description.interactor2.protein.accession, '/',
                                    description.interactor2.name,
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
                                <a href={editUrl(i)}>
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
                        Are you sure you want to delete this description?
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

export const Table = (descriptions: Description[]) => {
    return <StatelessTable descriptions={descriptions} />
}
