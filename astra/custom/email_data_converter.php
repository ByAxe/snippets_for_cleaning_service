<?php


/**
 * Converts order data to readable string
 * @param $orderData
 * @return string
 */
function convertOrderDataToString($orderData)
{
    $result = "<table>";
    $result .= "<thead>";
    $result .= "<tr><th>Поле</th><th>Значение</th></tr>";
    $result .= "</thead>";
    $result .= "<tbody>";
    foreach ($orderData as $field => $value) {
        $result .= "<tr><td>";

        switch ($field) {
            case FIELD_ORDER_ID["f"]:
                $result .= FIELD_ORDER_ID["n"];
                break;
            case FIELD_DT_CREATE["f"]:
                $result .= FIELD_DT_CREATE["n"];
                break;
            case FIELD_ORDER_DATE["f"]:
                $result .= FIELD_ORDER_DATE["n"];
                break;
            case FIELD_FREQUENCY["f"]:
                $result .= FIELD_FREQUENCY["n"];
                break;
            case FIELD_DISCOUNT["f"]:
                $result .= FIELD_DISCOUNT["n"];
                break;
            case FIELD_CLEANING_TYPE["f"]:
                $result .= FIELD_CLEANING_TYPE["n"];
                break;
            case FIELD_CUSTOMER_NAME["f"]:
                $result .= FIELD_CUSTOMER_NAME["n"];
                break;
            case FIELD_CUSTOMER_PHONE["f"]:
                $result .= FIELD_CUSTOMER_PHONE["n"];
                break;
            case FIELD_CUSTOMER_ADDRESS["f"]:
                $result .= FIELD_CUSTOMER_ADDRESS["n"];
                break;
            case FIELD_CUSTOMER_EMAIL["f"]:
                $result .= FIELD_CUSTOMER_EMAIL["n"];
                break;
            case FIELD_CUSTOMER_PAYLOAD["f"]:
                $result .= FIELD_CUSTOMER_PAYLOAD["n"];
                break;
        }

        $result .= "</td><td>$value</td>";
        $result .= "</tr>";
    }

    $result .= "</tbody>";
    $result .= "</table>";


    return $result;
}

/**
 * Converts order services to readable string
 * @param $orderServices
 * @return string
 */
function convertOrderServicesToString($orderServices)
{
    // sort all named array by keys to preserve an order for table
    foreach ($orderServices as $namedArray) {
        ksort($namedArray);
    }

    $result = "<table>";
    $result .= "<thead>";

    // build headers for the table
    $result .= "<tr>
                    <th>№</th>
                    <th>" . FIELD_OS_TITLE["n"] . "</th>
                    <th>" . FIELD_OS_PRICE["n"] . "</th>
                    <th>" . FIELD_OS_DURATION["n"] . "</th>
                    <th>" . FIELD_AMOUNT["n"] . "</th>
                </tr>";

    $number = 1;

    // build rows of services
    foreach ($orderServices as $service) {
        $result .= "<tr>";

        foreach ($service as $field => $value) {

            $resultingValue = $value;

            // insert row numbers for id
            if ($field === "id") {
                $resultingValue = $number;
            } // we don't need to show whether is service available
            else if (FIELD_OS_IS_AVAILABLE["f"] === $field
                || FIELD_OS_DESCRIPTION["f"] === $field
                || FIELD_OS_IS_EXTRA["f"] === $field) {
                continue;
            }

            $result .= "<td>$resultingValue</td>";
        }
        $result .= "</tr>";
        $number++;
    }


    $summary = calculateOrderSummary(null, $orderServices);

    $totalCost = $summary["totalCost"];
    $totalTimeHours = $summary["totalTimeHours"];
    $requiredMastersAmount = $summary["requiredMastersAmount"];

    // Add sums to resulting string
    $result .= "<tr><td colspan='2'>СУММА ЗАКАЗА (БЕЗ УЧËТА СКИДКИ)</td><td><b>$totalCost</b></td></tr>";
    $result .= "<tr><td colspan='3'>ПРИМЕРНОЕ ВРЕМЯ ВЫПОЛНЕНИЯ ЗАКАЗА (ЧАСОВ) ОДНИМ МАСТЕРОМ</td><td><b>$totalTimeHours</b></td></tr>";
    $result .= "<tr><td colspan='4'>КОЛИЧЕСТВО ТРЕБУЕМЫХ МАСТЕРОВ НА ЗАКАЗ</td><td><b>$requiredMastersAmount</b></td></tr>";

    // close main tags of table
    $result .= "</tbody>";
    $result .= "</table>";

    return $result;
}