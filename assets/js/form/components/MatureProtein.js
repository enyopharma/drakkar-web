import React, { useState } from 'react'

import CoordinateField from './CoordinateField'
import ExtractFormGroup from './ExtractFormGroup'
import MatureProteinList from './MatureProteinList'
import SubsequenceFormGroup from './SubsequenceFormGroup'

const MatureProtein = ({ name, start, stop, protein, update }) => {
    const [lname, setName] = useState(name)
    const [lstart, setStart] = useState(start)
    const [lstop, setStop] = useState(stop)

    const setCoordinates = (start, stop) => {
        setStart(start)
        setStop(stop)
    }

    const isNameSet = lname.trim() != ''

    const areCoordinatesSet = lstart != '' && lstop != ''

    const doesNameExist = protein.matures.filter(m => {
        return m.name == lname.trim()
    }).length > 0

    const doCoordinatesExist = protein.matures.filter(m => {
        return m.start == lstart && m.stop == lstop
    }).length > 0

    const doesMatureExist = protein.matures.filter(m => {
        return m.name == lname.trim() && m.start == lstart && m.stop == lstop
    }).length > 0

    const isNameValid = ! isNameSet || ! doesNameExist || doesMatureExist

    const areCoordinatesValid = ! areCoordinatesSet || ! doCoordinatesExist || doesMatureExist

    const isMatureValid = isNameSet && areCoordinatesSet
        && (doesMatureExist || (! doesNameExist && ! doCoordinatesExist))

    const handleValidate = () => {
        update({name: lname.trim(), start: lstart, stop: lstop})
    }

    const handleReset = () => {
        setCoordinates(1, protein.sequence.length)
    }

    return (
        <React.Fragment>
            {protein.matures.length == 0 ? (
                <p>
                    No sequence defined on this uniprot entry yet.
                </p>
            ) : (
                <React.Fragment>
                    <p>
                        Existing sequences on this uniprot entry:
                    </p>
                    <MatureProteinList matures={protein.matures} select={update} />
                </React.Fragment>
            )}
            <div className="row">
                <div className="col-3">
                    <input
                        type="text"
                        className={'form-control' + (isNameValid ? '' : ' is-invalid')}
                        placeholder="Name"
                        value={lname}
                        onChange={e => setName(e.target.value)}
                    />
                </div>
                <div className="col-3">
                    <CoordinateField
                        value={lstart}
                        set={setStart}
                        max={protein.sequence.length}
                        valid={areCoordinatesValid}
                    >
                        Start
                    </CoordinateField>
                </div>
                <div className="col-3">
                    <CoordinateField
                        value={lstop}
                        set={setStop}
                        max={protein.sequence.length}
                        valid={areCoordinatesValid}
                    >
                        Stop
                    </CoordinateField>
                </div>
                <div className="col-3">
                    <button
                        type="button"
                        className="btn btn-block btn-primary"
                        onClick={handleValidate}
                        disabled={! isMatureValid}
                    >
                        Validate
                    </button>
                </div>
            </div>
            <SubsequenceFormGroup sequence={protein.sequence} set={setCoordinates}>
                Extract coordinates
            </SubsequenceFormGroup>
            <ExtractFormGroup sequence={protein.sequence} set={setCoordinates}>
                Extract coordinates
            </ExtractFormGroup>
            <div className="row">
                <div className="col offset-9">
                    <button
                        type="button"
                        className="btn btn-block btn-info"
                        onClick={handleReset}
                    >
                        Set to full length
                    </button>
                </div>
            </div>
        </React.Fragment>
    )
}

export default MatureProtein;
