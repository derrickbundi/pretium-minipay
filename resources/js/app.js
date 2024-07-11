import './bootstrap'
import { getContract, formatEther, createPublicClient, http, createWalletClient, custom, encodeFunctionData, parseUnits } from 'viem'
import { celo } from 'viem/chains'
import { stableTokenABI } from '@celo/abis'

const STABLE_TOKEN_ADDRESS = "0x765DE816845861e75A25fCA122bb6898B8B1282a"
const PRETIUM_WALLET = "xxxxx"
let address = null
let exchange_rate, currency_code

document.addEventListener('DOMContentLoaded', async function() {
    const country = localStorage.getItem('selected_country') || "ke"

    function getExchangeRates(country) {
        return new Promise((resolve, reject) => {
            $.ajax({
                url: "https://minipay.pretium.africa/mpesa/get/rates/" + country,
                method: 'GET',
                success: function(response) {
                    resolve(response);
                },
                error: function(error) {
                    reject(error);
                }
            });
        });
    }

    let exchangeRateResponse;
    try {
        exchangeRateResponse = await getExchangeRates(country)

        exchange_rate = exchangeRateResponse.exchange_rate
        localStorage.setItem('currency_code', exchangeRateResponse.currency_code)
        currency_code = exchangeRateResponse.currency_code
    } catch (error) {
        console.log("Error fetching exchange rates:", error);
        return;
    }
    
    if(window && window.ethereum) {
        if (window.ethereum.isMiniPay) {
            let accounts = await window.ethereum.request({
                method: "eth_requestAccounts",
                params: [],
            });
            address = accounts[0];
            localStorage.setItem('address', address)
            let balance_in_fiat
            try {
                let balance = await checkCUSDBalance(publicClient, address)
                balance = Math.round(balance * 100) / 100
                balance_in_fiat = Math.round((balance * exchange_rate) * 100) / 100

                localStorage.setItem('balance_in_fiat', balance_in_fiat)

                document.getElementById('balance').innerText = balance
                document.getElementById('balance_in_fiat').innerText = balance_in_fiat
            } catch (error) {
                const balance = "0.00";
                document.getElementById('balance').innerText = balance
                document.getElementById('balance_in_fiat').innerText = balance
            }
        } else {
            const balance = "0.00";
            document.getElementById('balance').innerText = balance
            document.getElementById('balance_in_fiat').innerText = balance
        }
    } else {
        const balance = "0.00";
        document.getElementById('balance').innerText = balance
        document.getElementById('balance_in_fiat').innerText = balance
    }

    window.ethereumAddress = address;
    window.currencyCode = currency_code
});

document.addEventListener('DOMContentLoaded', function() {
    $('#myForm').on('submit', async function(event) {
        event.preventDefault();

        $(this).find('button[type="submit"]').prop('disabled', true)
        var loader = document.getElementById('loader')
        loader.style.display = "block"

        let flagged = await fetch('https://pretium.africa/api/flagged-paybills?all=true');
        flagged = await flagged.json();

        if(flagged.paybills.includes($('#shortcode').val())) {
            loader.style.display = "none";
            $(this).find('button[type="submit"]').prop('disabled', false);

            Toastify({
                text: "Payments to this Paybill are not supported.",
                duration: 5000,
                newWindow: true,
                close: true,
                gravity: "top",
                position: "center",
                stopOnFocus: true,
                style: {
                    background: "#f06548",
                },
            }).showToast();

            return;
        }

        let amount = parseFloat($('#amount').val())
        let fee = (0.8 / 100) * amount
        fee = parseFloat(fee)
        fee = fee < 1 ? 1 : fee

        const total_amount = amount + fee
        const cusd = total_amount / parseFloat(exchange_rate)

        try {
            let transferResult = await requestTransfer(cusd);

            if (transferResult.status === "success") {
                $.ajax({
                    url: "https://minipay.pretium.africa/mpesa/pay",
                    type: "GET",
                    data: {
                        amount: amount,
                        amount_in_usd: cusd,
                        fee: fee,
                        shortcode: $('#shortcode').val(),
                        account_number: $('#account_number').val(),
                        favorite: $('#favorite').prop('checked'),
                        mobile: $('#mobile').val(),
                        hash: transferResult.hash,
                        status: transferResult.status,
                        address: address
                    },
                    success: function(response) {
                        Toastify({
                            text: response.message,
                            duration: 4000,
                            newWindow: true,
                            close: true,
                            gravity: "top",
                            position: "center",
                            stopOnFocus: true,
                            style: {
                                background: response.status === "PENDING" ? "#0ab39c" : "#f06548",
                            },
                        }).showToast();

                        loader.style.display = "none";

                        setTimeout(function() {
                            window.location.href = "https://minipay.pretium.africa/mpesa/review/" + transferResult.hash;
                        }, 2000);
                    },
                    error: function(xhr, status, error) {                            
                        var response_error = 'An error occurred while submitting the form.';

                        if(xhr.responseJSON) {
                            response_error = xhr.responseJSON.message || response_error;
                        }
                        Toastify({
                            text: response_error,
                            duration: 4000,
                            newWindow: true,
                            close: true,
                            gravity: "top",
                            position: "center",
                            stopOnFocus: true,
                            style: {
                                background: "#f06548",
                            },
                        }).showToast();

                        loader.style.display = "none";

                        setTimeout(function() {
                            window.location.href = "https://minipay.pretium.africa/mpesa/review/" + transferResult.hash;
                        }, 2000);
                    }
                });
            } else {
                Toastify({
                    text: "Transaction failed.",
                    duration: 4000,
                    newWindow: true,
                    close: true,
                    gravity: "top",
                    position: "center",
                    stopOnFocus: true,
                    style: {
                        background: "#f06548",
                    },
                }).showToast();

                loader.style.display = "none";
                $(this).find('button[type="submit"]').prop('disabled', false);
            }
        } catch (error) {
            let errorMessage = "An error occurred during the transaction.";
            if(error && error.message) {
                const errorText = error.message.toLowerCase();
                if (errorText.includes("transfer amount exceeds balance") || errorText.includes("nonce too low")) {
                    errorMessage = "Failed! Insufficient funds.";
                }
            }

            $.ajax({
                url: "https://minipay.pretium.africa/mpesa/log/error",
                type: "POST",
                data: {
                    amount_in_usd: cusd,
                    amount: amount,
                    fee: fee,
                    shortcode: $('#shortcode').val(),
                    account_number: $('#account_number').val(),
                    mobile: $('#mobile').val(),
                    address: address,
                    error: error
                }
            });

            Toastify({
                text: errorMessage,
                duration: 4000,
                newWindow: true,
                close: true,
                gravity: "top",
                position: "center",
                stopOnFocus: true,
                style: {
                    background: "#f06548",
                },
            }).showToast();

            loader.style.display = "none";
            $(this).find('button[type="submit"]').prop('disabled', false);
        }
    }); 
});

document.addEventListener('DOMContentLoaded', function() {
    $('#airtimeForm').on('submit', async function(event) {
        event.preventDefault();

        $(this).find('button[type="submit"]').prop('disabled', true)
        var loader = document.getElementById('loader')
        loader.style.display = "block"

        let amount = parseFloat($('#amount').val())
        let fee = (0.8 / 100) * amount
        fee = parseFloat(fee)

        const total_amount = amount + fee
        const cusd = total_amount / parseFloat(exchange_rate)

        try {
            let transferResult = await requestTransfer(cusd);

            if (transferResult.status === "success") {
                $.ajax({
                    url: "https://minipay.pretium.africa/airtime/pay",
                    type: "GET",
                    data: {
                        amount: amount,
                        amount_in_usd: cusd,
                        fee: fee,
                        mobile: $('#mobile').val(),
                        hash: transferResult.hash,
                        status: transferResult.status,
                        address: address,
                        currency_code: currency_code
                    },
                    success: function(response) {
                        Toastify({
                            text: response.message,
                            duration: 4000,
                            newWindow: true,
                            close: true,
                            gravity: "top",
                            position: "center",
                            stopOnFocus: true,
                            style: {
                                background: response.status === "PENDING" ? "#0ab39c" : "#f06548",
                            },
                        }).showToast();

                        loader.style.display = "none";

                        setTimeout(function() {
                            window.location.href = "https://minipay.pretium.africa/mpesa/review/" + transferResult.hash;
                        }, 2000);
                    },
                    error: function(xhr, status, error) {                            
                        var response_error = 'An error occurred while submitting the form.';

                        if(xhr.responseJSON) {
                            response_error = xhr.responseJSON.message || response_error;
                        }
                        Toastify({
                            text: response_error,
                            duration: 4000,
                            newWindow: true,
                            close: true,
                            gravity: "top",
                            position: "center",
                            stopOnFocus: true,
                            style: {
                                background: "#f06548",
                            },
                        }).showToast();

                        loader.style.display = "none";

                        setTimeout(function() {
                            window.location.href = "https://minipay.pretium.africa/mpesa/review/" + transferResult.hash;
                        }, 2000);
                    }
                });
            } else {
                Toastify({
                    text: "Transaction failed.",
                    duration: 4000,
                    newWindow: true,
                    close: true,
                    gravity: "top",
                    position: "center",
                    stopOnFocus: true,
                    style: {
                        background: "#f06548",
                    },
                }).showToast();

                loader.style.display = "none";
                $(this).find('button[type="submit"]').prop('disabled', false);
            }
        } catch (error) {
            let errorMessage = "An error occurred during the transaction.";
            if(error && error.message) {
                const errorText = error.message.toLowerCase();
                if (errorText.includes("transfer amount exceeds balance") || errorText.includes("nonce too low")) {
                    errorMessage = "Failed! Insufficient funds.";
                }
            }

            $.ajax({
                url: "https://minipay.pretium.africa/mpesa/log/error",
                type: "POST",
                data: {
                    amount_in_usd: cusd,
                    amount: amount,
                    fee: fee,
                    shortcode: $('#shortcode').val(),
                    account_number: $('#account_number').val(),
                    mobile: $('#mobile').val(),
                    address: address,
                    error: error
                }
            });

            Toastify({
                text: errorMessage,
                duration: 4000,
                newWindow: true,
                close: true,
                gravity: "top",
                position: "center",
                stopOnFocus: true,
                style: {
                    background: "#f06548",
                },
            }).showToast();

            loader.style.display = "none";
            $(this).find('button[type="submit"]').prop('disabled', false);
        }
    }); 
});

async function checkCUSDBalance(publicClient, address) {
    const StableTokenContract = getContract({
        abi: stableTokenABI,
        address: STABLE_TOKEN_ADDRESS,
        publicClient
    });

    const balanceInBigNumber = await StableTokenContract.read.balanceOf([address]);
    const balanceInWei = balanceInBigNumber.toString();
    const balanceInEthers = formatEther(balanceInWei);

    return balanceInEthers;
}

const client = createWalletClient({
    chain: celo,
    transport: custom(window.ethereum)
});

const publicClient = createPublicClient({
    chain: celo,
    transport: http(),
});

async function requestTransfer(amount) {
    let hash = await client.sendTransaction({
        account: address,
        to: STABLE_TOKEN_ADDRESS,
        data: encodeFunctionData({
            abi: stableTokenABI,
            functionName: "transfer",
            args: [
                PRETIUM_WALLET,
                parseUnits(`${Number(amount)}`,18)
            ]
        }),
        chain: celo
    });

    const transaction = await publicClient.waitForTransactionReceipt({
        hash
    });

    return {
        hash: hash,
        status: transaction.status === "success" ? "success" : "failed"
    };
}


